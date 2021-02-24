<?php
namespace App\Modules\Lease\Repositories;

use App\Modules\Core\Classes\Repository;
use App\Modules\Core\Interfaces\IYardiImport;
use App\Modules\Core\Library\EloquentHelper;
use App\Modules\Edits\Models\ReviewStatus;
use App\Modules\Lease\Models\Lease;
use App\Modules\Lease\Models\TenantStatus;
use App\Modules\Lease\Models\Tenant;
use Illuminate\Support\Collection;
use App\Modules\Attachments\Traits\RepoHasAttachments;

class TenantRepository extends Repository implements IYardiImport
{
    use RepoHasAttachments;
    /**
     * TenantRepository constructor.
     * @param Tenant $model
     */
    public function __construct(Tenant $model)
    {
        $this->model = $model;
    }

    /**
     * @return Collection
     */
    public function getTenants(): Collection
    {
        return $this->model->orderBy($this->model->getTableName() . '.name', 'asc')->get();
    }

    /**
     * @param $id
     * @return Tenant
     */
    public function getTenant($id): Tenant
    {
        return $this->model->with([
            'reviewStatus',
            'lockedByUser',
        ])->findOrFail($id);
    }

    /**
     * @param array $data
     * @return Tenant
     */
    public function storeTenant(array $data): Tenant
    {
        $data['review_status_id'] = EloquentHelper::getRecordIdBySlug(
            ReviewStatus::class,
            ReviewStatus::NEVER_REVIEWED
        );
        return $this->model->create($data);
    }

    /**
     * @param int $id
     * @param array $data
     * @return Tenant
     */
    public function updateTenant(int $id, array $data): Tenant
    {
        $Tenant = $this->getTenant($id);
        $Tenant->update($data);
        return $Tenant;
    }

    /**
     * @param int $id
     * @return Tenant
     * @throws \Exception
     */
    public function deleteTenant(int $id): Tenant
    {
        $Tenant = $this->getTenant($id);
        $Tenant->delete();
        return $Tenant;
    }

    /**
     * @param array $data
     * @return Tenant|mixed
     */
    public function importRecord(array $data)
    {
        $status = TenantStatus::where('name', $data['status'])->first();
        $lease  = Lease::where(
            'cluttons_lease_ref',
            $data['tenant_ref']
        )->first();
        if (!$lease) {
            return;
        }

        return $this->storeTenant([
            'tenant_status_id'     => $status->id,
            'lease_id'             => $lease->id,
            'name'                 => $data['lt_name'],
            'yardi_tenant_ref'     => $data['tenant_ref'],
            'yardi_tenant_alt_ref' => $data['alt_ref'],
        ]);
    }

    /**
     * @param string $searchTerm
     * @return Tenant|mixed
     */
    public function search($searchTerm)
    {
        return $this->model
            ->select([
                'id',
                'name',
                'lease_id',
            ])
            ->orWhere('name', 'LIKE', "%{$searchTerm}%")
            ->with('lease.unit.property')
            ->get();
    }
}
