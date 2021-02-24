<?php
namespace App\Modules\Lease\Repositories;

use App\Modules\Core\Interfaces\IYardiImport;
use App\Modules\Lease\Models\PaidStatus;
use Illuminate\Support\Collection;

class PaidStatusRepository implements IYardiImport
{

    /**
     * @var PaidStatus
     */
    protected $model;

    /**
     * PaidStatusRepository constructor.
     * @param PaidStatus $model
     */
    public function __construct(PaidStatus $model)
    {
        $this->model = $model;
    }

    /**
     * @return Collection
     */
    public function getPaidStatuses(): Collection
    {
        return $this->model->orderBy('name', 'asc')->get();
    }

    /**
     * @param $id
     * @return PaidStatus
     */
    public function getPaidStatus(int $id): PaidStatus
    {
        return $this->model->findOrFail($id);
    }

    /**
     * @param array $data
     * @return PaidStatus
     */
    public function storePaidStatus(array $data): PaidStatus
    {
        return $this->model->create($data);
    }

    /**
     * @param int $id
     * @param array $data
     * @return PaidStatus
     */
    public function updatePaidStatus(int $id, array $data): PaidStatus
    {
        $PaidStatus = $this->getPaidStatus($id);
        $PaidStatus->update($data);
        return $PaidStatus;
    }

    /**
     * @param int $id
     * @return PaidStatus
     * @throws \Exception
     */
    public function deletePaidStatus(int $id): PaidStatus
    {
        $PaidStatus = $this->getPaidStatus($id);
        $PaidStatus->delete();
        return $PaidStatus;
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function importRecord(array $data)
    {
        if ($this->model->where('name', $data['name'])->get()->isEmpty()) {
            return $this->storePaidStatus($data);
        }
    }
}
