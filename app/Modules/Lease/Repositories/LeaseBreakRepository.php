<?php
namespace App\Modules\Lease\Repositories;

use App\Modules\Core\Interfaces\IYardiImport;
use App\Modules\Lease\Models\LeaseBreak;
use App\Modules\Lease\Models\BreakPartyOption;
use App\Modules\Lease\Models\Lease;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class LeaseBreakRepository implements IYardiImport
{
    /**
     * @var LeaseBreak
     */
    protected $model;

    /**
     * BreakPartyOptionRepository constructor.
     * @param LeaseBreak $model
     */
    public function __construct(LeaseBreak $model)
    {
        $this->model = $model;
    }

    public function get() : Collection
    {
        return $this->model->all();
    }

    public function getForType(string $type) : Collection
    {
        $notDate = $this->model
            ->where('entity_type', $type)
            ->where('type', '!=', 'Date')
            ->get();

        $date = $this->model
            ->where('entity_type', $type)
            ->where('type', '=', 'Date')
            ->where('date', '>=', Carbon::now())
            ->get();

        $query = $notDate->merge($date);
        return $query;
    }

    public function importRecord(array $data)
    {
        $data['penalty'] = \Helpers::convertStringToBool($data['penalty']);

        if (!$data['min_notice']) {
            $data['min_notice'] = 0;
        }
        $option = BreakPartyOption::where('name', $data['option'])->first();
        $data['break_party_option_id'] = $option->id;

        $lease = Lease::where('cluttons_lease_ref', $data['lease_ref'])->first();
        $data['entity_type'] = Lease::class;

        if (!$lease) {
            return;
        }
        $data['entity_id'] = $lease->id;
        
        return $this->model->create($data);
    }
}
