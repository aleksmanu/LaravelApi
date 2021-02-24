<?php
namespace App\Console\Commands;

use App\Modules\Acquisition\Models\Acquisition;
use App\Modules\Property\Models\Property;
use App\Modules\Property\Models\Unit;
use Carbon\Carbon;
use Illuminate\Console\Command;

class PerformanceCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'o:precache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Precaches database intensive data (such as aggregates).';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->warn('');
        $this->warn('');
        $this->warn('');
        $this->warn('Flushing Cache...');
        \Cache::flush();
        $this->warn('Cache Flushed.');
        $this->warn('');
        $this->warn('');
        $this->warn('');
        $this->warn('Precache process started. This might take a while, please wait...');

        // Load all properties and units, should force load all rentPerAnnums
        $props = Property::all();
        $units = Unit::all();
        $realUnits = collect([]);
        $fakeUnits = collect([]);
        foreach ($props as $prop) {
            $prop->toArray();
        }
        foreach ($units as $unit) {
            $unit->toArray();

            $unit->is_virtual ? $fakeUnits[] = $unit : $realUnits[] = $unit;
        }

        $acquis = Acquisition::all();
        $areaNum = 0;
        $siteNum = 0;
        $stepNum = 0;
        foreach ($acquis as $acquisition) {
            foreach ($acquisition->popAreas as $popArea) {
                $areaNum++;
                $popArea->toArray(); //force 'appends' things to load and cache
                foreach ($popArea->bareSites as $site) {
                    $siteNum++;
                    $site->toArray(); //force 'appends' things to load and cache
                    foreach ($site->bareSteps as $step) {
                        $stepNum++;
                        $step->toArray();
                    }
                }
            }
        }

        $this->warn('Precaching completed. Prepared data for: \n');
        $this->info(count($props) . ' Properties with');
        $this->info(
            '   £' . $props->sum('rentPerAnnum') .
            ' RPA, £' . $props->sum('serviceChargePerAnnum') . ' SCPA '
        );
        $this->info(
            '   £' . $props->sum('payableRentPerAnnum') .
            ' pRPA, £' . $props->sum('payableServiceChargePerAnnum') . ' pSCPA '
        );

        $this->info(
            count($realUnits) . ' Units with £' . $realUnits->sum('rentPerAnnum') .
            ' RPA, £' . $realUnits->sum('serviceChargePerAnnum') . ' SCPA '
        );
        $this->info(
            count($fakeUnits) . ' Virtual Units (payables) with £' . $fakeUnits->sum('rentPerAnnum') .
            ' RPA, £' . $fakeUnits->sum('serviceChargePerAnnum') . ' SCPA '
        );
        $this->info(
            count($acquis) . ' Acquisitions with ' . $areaNum .
            ' Pop Areas and ' . $siteNum . ' Sites totaling ' . $stepNum . ' Steps.'
        );
    }
}
