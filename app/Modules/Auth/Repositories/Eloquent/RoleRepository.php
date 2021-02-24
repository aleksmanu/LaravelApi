<?php
namespace App\Modules\Auth\Repositories\Eloquent;

use App\Modules\Auth\Models\Role;
use Illuminate\Support\Collection;

class RoleRepository
{

    /**
     * @var Role
     */
    protected $model;

    /**
     * RoleRepository constructor.
     * @param Role $model
     */
    public function __construct(Role $model)
    {
        $this->model = $model;
    }

    /**
     * @return Collection
     */
    public function getRoles(): Collection
    {
        return $this->model
            ->orderBy('name', 'asc')->get();
    }


    public function getInternalRoles()
    {
        return $this->model
            ->where('name', 'not like', 'e\_%')
            ->orderBy('name', 'asc')->get();
    }


    public function getExternalRoles(): Collection
    {
        return $this->model
            ->where('name', 'like', 'e\_%')
            ->orderBy('name', 'asc')
            ->get();
    }

    /**
     * @param $id
     * @return Role
     */
    public function getRole($id): Role
    {
        return $this->model->findOrFail($id);
    }
}
