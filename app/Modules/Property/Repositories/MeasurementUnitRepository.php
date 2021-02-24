<?php
namespace App\Modules\Property\Repositories;

use App\Modules\Core\Interfaces\IYardiImport;

use App\Modules\Property\Models\MeasurementUnit;
use Illuminate\Support\Collection;

class MeasurementUnitRepository implements IYardiImport
{
    /**
     * @var MeasurementUnit
     */
    protected $model;

    /**
     * MeasurementUnitRepository constructor.
     * @param MeasurementUnit $model
     */
    public function __construct(MeasurementUnit $model)
    {
        $this->model = $model;
    }

    /**
     * @return Collection
     */
    public function getMeasurementUnits(): Collection
    {
        return $this->model->orderBy('name', 'asc')->get();
    }

    /**
     * @param $id
     * @return MeasurementUnit
     */
    public function getMeasurementUnit($id): MeasurementUnit
    {
        return $this->model->findOrFail($id);
    }

    /**
     * @param array $data
     * @return MeasurementUnit
     */
    public function storeMeasurementUnit(array $data): MeasurementUnit
    {
        return $this->model->create($data);
    }

    /**
     * @param int $id
     * @param array $data
     * @return MeasurementUnit
     */
    public function updateMeasurementUnit(int $id, array $data): MeasurementUnit
    {
        $MeasurementUnit = $this->getMeasurementUnit($id);
        $MeasurementUnit->update($data);
        return $MeasurementUnit;
    }

    /**
     * @param int $id
     * @return MeasurementUnit
     * @throws \Exception
     */
    public function deleteMeasurementUnit(int $id): MeasurementUnit
    {
        $MeasurementUnit = $this->getMeasurementUnit($id);
        $MeasurementUnit->delete();
        return $MeasurementUnit;
    }

    /**
     * @param array $data
     * @return MeasurementUnit|mixed
     */
    public function importRecord(array $data)
    {
        if ($this->model->where('name', $data['name'])->get()->isEmpty()) {
            return $this->storeMeasurementUnit($data);
        }
    }
}
