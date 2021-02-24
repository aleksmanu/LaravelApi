<?php
namespace App\Modules\Lease\Repositories;

use App\Modules\Core\Interfaces\IYardiImport;
use App\Modules\Lease\Models\RentFrequency;
use Illuminate\Support\Collection;

class RentFrequencyRepository implements IYardiImport
{
    /**
     * @var RentFrequency
     */
    protected $model;

    /**
     * RentFrequencyRepository constructor.
     * @param RentFrequency $model
     */
    public function __construct(RentFrequency $model)
    {
        $this->model = $model;
    }

    /**
     * @return Collection
     */
    public function getRentFrequencies(): Collection
    {
        return $this->model
            ->select($this->model->getTableName() . '.*')
            ->orderBy($this->model->getTableName() . '.name', 'asc')
            ->groupBy($this->model->getTableName() . '.id')
            ->get();
    }

    /**
     * @param int $id
     * @return RentFrequency
     */
    public function getRentFrequency(int $id): RentFrequency
    {
        return $this->model
            ->select($this->model->getTableName() . '.*')
            ->groupBy($this->model->getTableName() . '.id')
            ->findOrFail($id);
    }

    /**
     * @param array $data
     * @return RentFrequency
     */
    public function storeRentFrequency(array $data): RentFrequency
    {
        $data['slug'] = $this->generateSlug($data['name']);
        return $this->model->create($data);
    }

    /**
     * @param int $id
     * @param array $data
     * @return RentFrequency
     */
    public function updateRentFrequency(int $id, array $data): RentFrequency
    {
        $RentFrequency = $this->getRentFrequency($id);
        $data['slug'] = $this->generateSlug($data['name']);
        $RentFrequency->update($data);
        return $RentFrequency;
    }

    /**
     * @param int $id
     * @return RentFrequency
     * @throws \Exception
     */
    public function deleteRentFrequency(int $id): RentFrequency
    {
        $RentFrequency = $this->getRentFrequency($id);
        $RentFrequency->delete();
        return $RentFrequency;
    }

    /** Generates a unique slug by appending incrementing numbers to the end if duplicates exist
     * @param string $name
     * @return string
     */
    public function generateSlug(string $name): string
    {
        $slug = str_slug($name);

        if (RentFrequency::where('slug', $slug)->exists()) {
            $suffix = 1;
            while (($slug = str_slug($name . '-' . $suffix)) && RentFrequency::where('slug', $slug)->exists()) {
                $suffix++;
            }
        }

        return $slug;
    }

    /**
     * @param array $data
     * @return RentFrequency|mixed
     */
    public function importRecord(array $data)
    {
        if ($this->model->where('name', $data['name'])->get()->isEmpty()) {
            return $this->storeRentFrequency($data);
        }
    }
}
