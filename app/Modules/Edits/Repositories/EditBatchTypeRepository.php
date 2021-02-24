<?php
namespace App\Modules\Edits\Repositories;

use App\Modules\Edits\Models\EditBatchType;

class EditBatchTypeRepository
{

    /**
     * @var EditBatchType
     */
    protected $model;

    /**
     * EditBatchTypeRepository constructor.
     * @param EditBatchType $model
     */
    public function __construct(EditBatchType $model)
    {
        $this->model = $model;
    }

    /**
     * @return mixed
     */
    public function findAll()
    {
        return $this->model->orderBy('name', 'asc')->get();
    }
}
