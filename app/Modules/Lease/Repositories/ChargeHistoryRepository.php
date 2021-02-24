<?php

namespace App\Modules\Lease\Repositories;

use Illuminate\Support\Collection;
use App\Modules\Lease\Models\ChargeHistory;
use App\Modules\Lease\Models\Lease;

class ChargeHistoryRepository
{
    /**
     * @var ChargeHistory
     */
    protected $model;

    public function __construct(ChargeHistory $model)
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
    public function index(array $options = []): Collection
    {
        $q = $this->query()
            ->select(ChargeHistory::getTableName() . '.*')
            ->orderBy(ChargeHistory::getTableName() . '.id', 'asc');

        if (count($options)) {
            $q->where($options);
        }

        return $q->get();
    }

    /**
     * @param int $id
     * @return ChargeHistory
     */
    public function get(int $id): ChargeHistory
    {
        return $this->query()
            ->select($this->model->getTableName() . '.*')
            ->findOrFail($id);
    }

    /**
     * @param array $data
     * @return ChargeHistory
     */
    public function store(array $data): ChargeHistory
    {
        return $this->model->create($data);
    }

    /**
     * @param array $data
     */
    public function importRecord(array $data)
    {
        $lease = Lease::where('cluttons_lease_ref', $data['lease_ref'])->first();
        $data['entity_type'] = Lease::class;
        if (!$lease) {
            return;
        }
        $data['entity_id'] = $lease->id;

        $data['lease_charge_type'] = $data['type_id'];
       
        return $this->store($data);
    }
}
