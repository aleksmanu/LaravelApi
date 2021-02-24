<?php
namespace App\Modules\Workorder\Repositories\Interfaces;

use App\Modules\Workorder\Models\ExpenditureType;
use Illuminate\Support\Collection;

interface IExpenditureTypeRepository
{
    /**
     * @return Collection
     */
    public function list(): Collection;

    /**
     * @param int $id
     * @return ExpenditureType
     */
    public function get(int $id): ExpenditureType;

    /**
     * @param array $data
     * @return ExpenditureType
     */
    public function store(array $data): ExpenditureType;

    /**
     * @param int $id
     * @param array $data
     * @return ExpenditureType
     */
    public function update(int $id, array $data): ExpenditureType;

    /**
     * @param int $id
     * @return ExpenditureType
     */
    public function delete(int $id): ExpenditureType;
}
