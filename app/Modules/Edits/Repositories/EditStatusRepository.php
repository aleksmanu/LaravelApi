<?php
namespace App\Modules\Edits\Repositories;

use App\Modules\Edits\Models\EditStatus;

class EditStatusRepository
{

    /**
     * @var EditStatus
     */
    protected $model;

    /**
     * EditStatusRepository constructor.
     * @param EditStatus $model
     */
    public function __construct(EditStatus $model)
    {
        $this->model = $model;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection|mixed|static[]
     */
    public function findAll()
    {
        return $this->model->all();
    }

    /**
     * @param $id
     * @return mixed
     */
    public function findById($id)
    {
        return $this->model->findOrFail($id);
    }

    /**
     * @param $slug
     * @return mixed
     */
    public function findBySlug($slug)
    {
        return $this->model->where('slug', $slug)->first();
    }
}
