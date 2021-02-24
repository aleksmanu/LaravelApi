<?php
namespace App\Modules\Common\Repositories;

use App\Modules\Common\Models\County;
use App\Modules\Core\Interfaces\IYardiImport;
use Illuminate\Support\Collection;

class CountyRepository implements IYardiImport
{
    /**
     * @var County
     */
    protected $model;

    /**
     * CountyRepository constructor.
     * @param County $model
     */
    public function __construct(County $model)
    {
        $this->model = $model;
    }

    /**
     * @return Collection
     */
    public function getCounties(): Collection
    {
        return $this->model->orderBy('name', 'asc')->get();
    }

    /**
     * @param $id
     * @return County
     */
    public function getCounty($id): County
    {
        return $this->model->findOrFail($id);
    }

    /**
     * @param array $data
     * @return County
     */
    public function storeCounty(array $data): County
    {
        return $this->model->create($data);
    }

    /**
     * @param int $id
     * @param array $data
     * @return County
     */
    public function updateCounty(int $id, array $data): County
    {
        $County = $this->getCounty($id);
        $County->update($data);
        return $County;
    }

    /**
     * @param int $id
     * @return County
     * @throws \Exception
     */
    public function deleteCounty(int $id): County
    {
        $County = $this->getCounty($id);
        $County->delete();
        return $County;
    }

    /**
     * @param array $data
     * @return County|mixed
     */
    public function importRecord(array $data)
    {
        if ($this->model->where('name', $data['name'])->get()->isEmpty()) {
            return $this->storeCounty($data);
        }
    }
}
