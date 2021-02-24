<?php
namespace App\Modules\Property\Repositories;

use App\Modules\Core\Interfaces\IYardiImport;
use App\Modules\Core\Library\StringHelper;
use App\Modules\Property\Models\PropertyCategory;

class PropertyCategoryRepository implements IYardiImport
{

    /**
     * @var PropertyCategory
     */
    private $model;

    /**
     * PropertyCategoryRepository constructor.
     * @param PropertyCategory $model
     */
    public function __construct(PropertyCategory $model)
    {
        $this->model = $model;
    }

    /**
     * @return PropertyCategory[]|\Illuminate\Database\Eloquent\Collection|mixed
     */
    public function getPropertyCategories()
    {
        return $this->model
            ->select($this->model->getTableName() . '.*')
            ->orderBy($this->model->getTableName() . '.name', 'asc')
            ->groupBy($this->model->getTableName() . '.id')
            ->get();
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function getPropertyCategory(int $id)
    {
        return $this->model
            ->select($this->model->getTableName() . '.*')
            ->orderBy($this->model->getTableName() . '.name', 'asc')
            ->groupBy($this->model->getTableName() . '.id')
            ->findOrFail($id);
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function storePropertyCategory(array $data)
    {

        $data['slug'] = StringHelper::slugify($data['name']);
        return $this->model->create($data);
    }

    /**
     * @param int $id
     * @param array $data
     * @return mixed
     */
    public function updatePropertyCategory(int $id, array $data)
    {
        $stopPosting = $this->model->findOrFail($id);
        $data['slug'] = StringHelper::slugify($data['name']);
        $stopPosting->update($data);
        return $stopPosting;
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function deletePropertyCategory(int $id)
    {
        $stopPosting = $this->model->findOrFail($id);
        $stopPosting->delete();
        return $stopPosting;
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function importRecord(array $data)
    {
        if ($this->model->where('name', $data['name'])->get()->isEmpty()) {
            return $this->storePropertyCategory($data);
        }
    }
}
