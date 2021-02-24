<?php

namespace App\Modules\Workorder\Repositories;

use App\Modules\Core\Interfaces\IYardiImport;
use App\Modules\Workorder\Models\Supplier;
use App\Modules\Workorder\Repositories\Interfaces\ISupplierRepository;
use Illuminate\Support\Collection;

class SupplierRepository implements ISupplierRepository, IYardiImport
{
    /**
     * @var Supplier
     */
    protected $model;

    /**
     * SupplierRepository constructor.
     * @param Supplier $model
     */
    public function __construct(Supplier $model)
    {
        $this->model = $model;
    }

    /**
     * @return Collection
     */
    public function list(): Collection
    {
        return $this->model->select($this->model::getTableName() . '.id', $this->model::getTableName() . '.name')->get();
    }

    /**
     * @param int $id
     * @return Supplier
     */
    public function get(int $id): Supplier
    {
        return $this->model->findOrFail($id);
    }

    /**
     * @param array $data
     * @return Supplier
     */
    public function store(array $data): Supplier
    {
        return $this->model->create($data);
    }

    /**
     * @param int $id
     * @param array $data
     * @return Supplier
     */
    public function update(int $id, array $data): Supplier
    {
        $supplier = $this->get($id);
        return $supplier;
    }

    /**
     * @param int $id
     * @return Quote
     * @throws \Exception
     */
    public function delete(int $id): Supplier
    {
        $supplier = $this->get($id);
        $supplier->delete();
        return $supplier;
    }

    /**
     * @param array $data
     * @return Supplier|mixed
     */
    public function importRecord(array $data)
    {
        if ($this->model->where('name', $data['name'])->get()->isEmpty()) {
            return $this->store($data);
        }
    }
}
