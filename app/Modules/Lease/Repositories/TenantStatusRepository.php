<?php
namespace App\Modules\Lease\Repositories;

use App\Modules\Core\Interfaces\IYardiImport;
use App\Modules\Core\Library\StringHelper;
use App\Modules\Lease\Models\TenantStatus;
use Illuminate\Support\Collection;

class TenantStatusRepository implements IYardiImport
{
    /**
     * @var TenantStatus
     */
    protected $model;

    /**
     * TenantStatusRepository constructor.
     * @param TenantStatus $model
     */
    public function __construct(TenantStatus $model)
    {
        $this->model = $model;
    }

    /**
     * @return Collection
     */
    public function getTenantStatuses(): Collection
    {
        return $this->model->orderBy('name', 'asc')->get();
    }

    /**
     * @param int $id
     * @return TenantStatus
     */
    public function getTenantStatus(int $id): TenantStatus
    {
        return $this->model->findOrFail($id);
    }

    /**
     * @param array $data
     * @return TenantStatus
     */
    public function storeTenantStatus(array $data): TenantStatus
    {
        $data['slug'] = StringHelper::slugify($data['name']);
        return $this->model->create($data);
    }

    /**
     * @param int $id
     * @param array $data
     * @return TenantStatus
     */
    public function updateTenantStatus(int $id, array $data): TenantStatus
    {
        $TenantStatus = $this->getTenantStatus($id);
        $TenantStatus->update($data);
        return $TenantStatus;
    }

    /**
     * @param int $id
     * @return TenantStatus
     * @throws \Exception
     */
    public function deleteTenantStatus(int $id): TenantStatus
    {
        $TenantStatus = $this->getTenantStatus($id);
        $TenantStatus->delete();
        return $TenantStatus;
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function importRecord(array $data)
    {
        if ($this->model->where('name', $data['name'])->get()->isEmpty()) {
            return $this->storeTenantStatus($data);
        }
    }
}
