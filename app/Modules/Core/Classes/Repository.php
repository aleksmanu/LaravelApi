<?php

namespace App\Modules\Core\Classes;

abstract class Repository
{

    /**
     * @var
     */
    protected $model;

    /**
     * @return string
     */
    public function getModelClass(): string
    {
        return get_class($this->model);
    }

    /**
     * @return string
     */
    public function getModelTable(): string
    {
        return $this->model->getTable();
    }

    /**
     * @return mixed
     */
    public function getModelObject()
    {
        return $this->model;
    }
}
