<?php
namespace App\Modules\Workorder\Repositories\Interfaces;

use App\Modules\Workorder\Models\Supplier;
use Illuminate\Support\Collection;

interface ISupplierRepository
{
    /**
     * @return Collection
     */
    public function list(): Collection;

    /**
     * @param int $id
     * @return Supplier
     */
    public function get(int $id): Supplier;

    /**
     * @param array $data
     * @return Supplier
     */
    public function store(array $data): Supplier;

    /**
     * @param int $id
     * @param array $data
     * @return Supplier
     */
    public function update(int $id, array $data): Supplier;

    /**
     * @param int $id
     * @return Supplier
     */
    public function delete(int $id): Supplier;
}
