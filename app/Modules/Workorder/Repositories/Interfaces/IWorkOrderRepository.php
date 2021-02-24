<?php
namespace App\Modules\Workorder\Repositories\Interfaces;

use App\Modules\Workorder\Models\WorkOrder;

interface IWorkOrderRepository
{
    /**
     * @return
     */
    public function list();

    /**
     * @param int $id
     * @return WorkOrder
     */
    public function get(int $id): WorkOrder;

    /**
     * @param array $data
     * @return WorkOrder
     */
    public function store(array $data): WorkOrder;

    /**
     * @param int $id
     * @param array $data
     * @return WorkOrder
     */
    public function update(int $id, array $data): WorkOrder;

    /**
     * @param int $id
     * @return WorkOrder
     */
    public function delete(int $id): WorkOrder;
}
