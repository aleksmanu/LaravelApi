<?php
namespace App\Console\Commands;

use App\Modules\Client\Repositories\ClientAccountRepository;
use App\Modules\Client\Repositories\ClientAccountStatusRepository;
use App\Modules\Client\Repositories\OrganisationTypeRepository;
use App\Modules\Client\Repositories\PortfolioRepository;
use App\Modules\Common\Repositories\CountryRepository;
use App\Modules\Common\Repositories\CountyRepository;
use App\Modules\Lease\Repositories\BreakPartyOptionRepository;
use App\Modules\Lease\Repositories\LeaseRepository;
use App\Modules\Lease\Repositories\LeaseTypeRepository;
use App\Modules\Lease\Repositories\PaidStatusRepository;
use App\Modules\Lease\Repositories\RentFrequencyRepository;
use App\Modules\Lease\Repositories\ReviewTypeRepository;
use App\Modules\Lease\Repositories\TenantRepository;
use App\Modules\Lease\Repositories\TenantStatusRepository;
use App\Modules\Lease\Repositories\TransactionRepository;
use App\Modules\Lease\Repositories\TransactionTypeRepository;
use App\Modules\Lease\Repositories\LeaseChargeTypeRepository;
use App\Modules\Property\Repositories\LocationTypeRepository;
use App\Modules\Property\Repositories\MeasurementUnitRepository;
use App\Modules\Property\Repositories\PropertyCategoryRepository;
use App\Modules\Property\Repositories\PropertyManagerRepository;
use App\Modules\Property\Repositories\PropertyRepository;
use App\Modules\Property\Repositories\PropertyStatusRepository;
use App\Modules\Property\Repositories\PropertyTenureRepository;
use App\Modules\Property\Repositories\PropertyUseRepository;
use App\Modules\Property\Repositories\StopPostingRepository;
use App\Modules\Property\Repositories\UnitRepository;
use App\Modules\Workorder\Repositories\SupplierRepository;
use App\Modules\Workorder\Repositories\ExpenditureTypeRepository;
use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Modules\Lease\Repositories\LeaseChargeRepository;
use App\Modules\Lease\Repositories\LeaseBreakRepository;
use App\Modules\Lease\Repositories\ChargeHistoryRepository;
use App\Modules\Lease\Repositories\ManagingAgentRepository;
use App\Modules\Lease\Repositories\LandlordRepository;

class YardiImport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'yardi:import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import Yardi Residential Data';

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
     * @throws \Exception
     * @throws \Throwable
     */
    public function handle()
    {
        \Cache::flush();

        $files = array_diff(scandir(storage_path() . '/app/import'), ['.', '..']);
        natsort($files);

        $this->info('import started at: ' . Carbon::now());
        \DB::transaction(function () use ($files) {

            foreach ($files as $file) {
                $trimmed          = trim(substr($file, strpos($file, '_') + 1));
                $trimmed_filename = preg_replace('/\\.[^.\\s]{3,4}$/', '', $trimmed);

                $file = storage_path() . '/app/import/' . $file;

                \Excel::load($file, function ($reader) use ($trimmed_filename) {
                    $this->import($reader, $trimmed_filename);
                });
            }
        });

        \Cache::flush();
        $this->info('import completed at: ' . Carbon::now());
    }

    /**
     * @param $reader
     * @param $trimmed_filename
     * @return bool
     * @throws \Exception
     */
    private function import($reader, $trimmed_filename)
    {
        $this->info('Importing file: ' . $trimmed_filename . '.csv');

        $repository = $this->getRepository($trimmed_filename);

        foreach ($reader->get() as $row) {
            $data = $row->toArray();

            $repository->importRecord($data);
        }
        return true;
    }

    /**
     * @param $sheet_name
     * @return mixed
     * @throws \Exception
     */
    private function getRepository($sheet_name)
    {

        $class = null;
        switch ($sheet_name) {
            case 'property_categories':
                $class = PropertyCategoryRepository::class;
                break;
            case 'organisation_types':
                $class = OrganisationTypeRepository::class;
                break;
            case 'client_statuses':
                $class = ClientAccountStatusRepository::class;
                break;
            case 'property_statuses':
                $class = PropertyStatusRepository::class;
                break;
            case 'property_tenures':
                $class = PropertyTenureRepository::class;
                break;
            case 'property_uses':
                $class = PropertyUseRepository::class;
                break;
            case 'location_types':
                $class = LocationTypeRepository::class;
                break;
            case 'measurement_units':
                $class = MeasurementUnitRepository::class;
                break;
            case 'lease_types':
            case 'lease_payable_types':
                $class = LeaseTypeRepository::class;
                break;
            case 'rent_frequencies':
                $class = RentFrequencyRepository::class;
                break;
            case 'review_types':
                $class = ReviewTypeRepository::class;
                break;
            case 'tenant_statuses':
                $class = TenantStatusRepository::class;
                break;
            case 'countries':
                $class = CountryRepository::class;
                break;
            case 'counties':
                $class = CountyRepository::class;
                break;
            case 'property_managers':
                $class = PropertyManagerRepository::class;
                break;
            case 'stop_postings':
                $class = StopPostingRepository::class;
                break;
            case 'clients':
                $class = ClientAccountRepository::class;
                break;
            case 'portfolios':
                $class = PortfolioRepository::class;
                break;
            case 'properties':
                $class = PropertyRepository::class;
                break;
            case 'units':
                $class = UnitRepository::class;
                break;
            case 'leases':
            case 'lease_payables':
                $class = LeaseRepository::class;
                break;
            case 'tenants':
                $class = TenantRepository::class;
                break;
            case 'paid_statuses':
                $class = PaidStatusRepository::class;
                break;
            case 'transaction_types':
                $class = TransactionTypeRepository::class;
                break;
            case 'transactions':
            case 'supplier_transactions':
                $class = TransactionRepository::class;
                break;
            case 'suppliers':
                $class = SupplierRepository::class;
                break;
            case 'expenditure_types':
                $class = ExpenditureTypeRepository::class;
                break;
            case 'lease_charge_types':
                $class = LeaseChargeTypeRepository::class;
                break;
            case 'break_party_options':
                $class = BreakPartyOptionRepository::class;
                break;
            case 'lease_charges':
                $class = LeaseChargeRepository::class;
                break;
            case 'break_data':
                $class = LeaseBreakRepository::class;
                break;
            case 'managing_agents':
                $class = ManagingAgentRepository::class;
                break;
            case 'landlords':
                $class = LandlordRepository::class;
                break;
            case 'charge_history':
                $class = ChargeHistoryRepository::class;
                break;
            default:
                throw new \Exception('Class not found for sheet: ' . $sheet_name);
        }

        return \App::make($class);
    }
}
