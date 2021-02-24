<?php
namespace App\Modules\Property\Repositories;

use Illuminate\Support\Collection;
use App\Modules\Core\Interfaces\IYardiImport;
use App\Modules\Core\Library\StringHelper;
use App\Modules\Property\Models\StopPosting;

class StopPostingRepository implements IYardiImport
{

    /**
     * @var StopPosting
     */
    private $model;

    /**
     * StopPostingRepository constructor.
     * @param StopPosting $model
     */
    public function __construct(StopPosting $model)
    {
        $this->model = $model;
    }

    public function getStopPostings(): Collection
    {
        return $this->model
            ->select($this->model->getTableName() . '.*')
            ->orderBy($this->model->getTableName() . '.name', 'asc')
            ->groupBy($this->model->getTableName() . '.id')
            ->get();
    }

    public function getStopPosting($id)
    {
        return $this->model
            ->select($this->model->getTableName() . '.*')
            ->groupBy($this->model->getTableName() . '.id')
            ->findOrFail($id);
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function storeStopPosting(array $data)
    {
        $data['slug'] = StringHelper::slugify($data['name']);
        return $this->model->create($data);
    }

    public function updateStopPosting($id, array $data)
    {
        $stopPosting = $this->model->findOrFail($id);
        $data['slug'] = StringHelper::slugify($data['name']);
        $stopPosting->update($data);
        return $stopPosting;
    }

    public function deleteStopPosting($id)
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
            return $this->storeStopPosting($data);
        }
    }
}
