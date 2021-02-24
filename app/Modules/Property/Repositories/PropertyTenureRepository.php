<?php
namespace App\Modules\Property\Repositories;

use Illuminate\Support\Collection;
use App\Modules\Core\Interfaces\IYardiImport;
use App\Modules\Core\Library\StringHelper;
use App\Modules\Property\Models\PropertyTenure;

class PropertyTenureRepository implements IYardiImport
{
    /**
     * @var PropertyTenure
     */
    protected $model;

    /**
     * TenureRepository constructor.
     * @param PropertyTenure $model
     */
    public function __construct(PropertyTenure $model)
    {
        $this->model = $model;
    }

    /**
     * @return Collection
     */
    public function getTenures(): Collection
    {
        return $this->model
            ->select($this->model->getTableName() . '.*')
            ->orderBy($this->model->getTableName() . '.name', 'asc')
            ->groupBy($this->model->getTableName() . '.id')
            ->get();
    }

    /**
     * @param $id
     * @return PropertyTenure
     */
    public function getTenure($id): PropertyTenure
    {
        return $this->model
            ->select($this->model->getTableName() . '.*')
            ->groupBy($this->model->getTableName() . '.id')
            ->findOrFail($id);
    }

    /**
     * @param array $data
     * @return PropertyTenure
     */
    public function storeTenure(array $data): PropertyTenure
    {
        $data['slug'] = StringHelper::slugify($data['name']);
        return $this->model->create($data);
    }

    /**
     * @param int $id
     * @param array $data
     * @return PropertyTenure
     */
    public function updateTenure(int $id, array $data): PropertyTenure
    {
        $tenure = $this->getTenure($id);
        $tenure->update($data);
        return $tenure;
    }

    /**
     * @param int $id
     * @return PropertyTenure
     * @throws \Exception
     */
    public function deleteTenure(int $id): PropertyTenure
    {
        $tenure = $this->getTenure($id);
        $tenure->delete();
        return $tenure;
    }

    public function importRecord(array $data)
    {
        if ($this->model->where('name', $data['name'])->get()->isEmpty()) {
            return $this->storeTenure($data);
        }
    }
}
