<?php

namespace App\Modules\Lease\Repositories;

use Illuminate\Support\Collection;
use App\Modules\Lease\Models\LeaseCharge;
use App\Modules\Lease\Models\Lease;

class LeaseChargeRepository
{
    /**
     * @var LeaseCharge
     */
    protected $model;

    public function __construct(LeaseCharge $model)
    {
        $this->model = $model;
    }

    private function query()
    {
        return $this->model->newQuery();
    }

    /**
     * @param array $options
     * @return Collection
     */
    public function getLeaseCharges(array $options = []): Collection
    {
        $q = $this->query()
            ->select(LeaseCharge::getTableName() . '.*')
            ->orderBy(LeaseCharge::getTableName() . '.id', 'asc');

        if (count($options)) {
            $q->where($options); // many thanks to Aidan for teaching me to be lazy
        } // you're welcome buddy

        return $q->get();
    }

    /**
     * @param int $id
     * @return LeaseCharge
     */
    public function getLeaseCharge(int $id): LeaseCharge
    {
        return $this->query()
            ->select($this->model->getTableName() . '.*')
            ->with([

            ])
            ->findOrFail($id);
    }

    /**
     * @param array $data
     * @return LeaseCharge
     */
    public function storeLeaseCharge(array $data): LeaseCharge
    {
        return $this->model->create($data);
    }

    /**
     * @param array $data
     */
    public function importRecord(array $data)
    {
        $lease = Lease::where('cluttons_lease_ref', $data['lease_id'])->first();
        $data['entity_type'] = Lease::class;
        if (!$lease) {
            return;
        }
        $data['entity_id'] = $lease->id;

        $data['lease_charge_type'] = $data['type_id'];

        $data['li_charged'] = $data['li_charged'] === "'true'";

        $charge = $this->storeLeaseCharge($data);

        $lease->passing_rent = $lease->rentCharges()->sum('annual');
        $lease->service_charge = $lease->serviceCharges()->sum('annual');
        $lease->rates_liability = $lease->rateCharges()->sum('annual');
        $lease->insurance = $lease->insuranceCharges()->sum('annual');
        $lease->save();
        
        return $charge;
    }
}
