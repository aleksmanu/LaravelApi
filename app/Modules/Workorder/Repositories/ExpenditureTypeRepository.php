<?php
namespace App\Modules\Workorder\Repositories;

use App\Modules\Core\Interfaces\IYardiImport;
use App\Modules\Workorder\Models\ExpenditureType;
use App\Modules\Workorder\Repositories\Interfaces\IExpenditureTypeRepository;
use Illuminate\Support\Collection;

class ExpenditureTypeRepository implements IExpenditureTypeRepository, IYardiImport
{
    /**
     * @var Quote
     */
    protected $model;

    /**
     * ExpenditureTypeRepository constructor.
     * @param ExpenditureType $model
     */
    public function __construct(ExpenditureType $model)
    {
        $this->model = $model;
    }

    /**
     * @return Collection
     */
    public function list(): Collection
    {
        return $this->model->get();
    }

    /**
     * @param int $id
     * @return ExpenditureType
     */
    public function get(int $id): ExpenditureType
    {
        return $this->model->findOrFail($id);
    }

    /**
     * @param array $data
     * @return ExpenditureType
     */
    public function store(array $data): ExpenditureType
    {
        return $this->model->create($data);
    }

    /**
     * @param int $id
     * @param array $data
     * @return ExpenditureType
     */
    public function update(int $id, array $data): ExpenditureType
    {
        $quote = $this->get($id);
        return $quote;
    }

    /**
     * @param int $id
     * @return ExpenditureType
     * @throws \Exception
     */
    public function delete(int $id): ExpenditureType
    {
        $expenditure_type = $this->get($id);
        $expenditure_type->delete();
        return $expenditure_type;
    }

    /**
     * @param array $data
     * @return ExpenditureType|mixed
     */
    public function importRecord(array $data)
    {
        if ($this->model->where('name', $data['name'])->get()->isEmpty()) {
            return $this->store($data);
        }
    }
}
