<?php
namespace App\Modules\Common\Repositories;

use App\Modules\Common\Models\Country;
use App\Modules\Core\Interfaces\IYardiImport;
use Illuminate\Support\Collection;

class CountryRepository implements IYardiImport
{
    /**
     * @var Country
     */
    protected $model;

    /**
     * CountryRepository constructor.
     * @param Country $model
     */
    public function __construct(Country $model)
    {
        $this->model = $model;
    }

    /**
     * @return Collection
     */
    public function getCountries(): Collection
    {
        return $this->model->orderBy('name', 'asc')->get();
    }

    /**
     * @param $id
     * @return Country
     */
    public function getCountry($id): Country
    {
        return $this->model->findOrFail($id);
    }

    /**
     * @param array $data
     * @return Country
     */
    public function storeCountry(array $data): Country
    {
        return $this->model->create($data);
    }

    /**
     * @param int $id
     * @param array $data
     * @return Country
     */
    public function updateCountry(int $id, array $data): Country
    {
        $Country = $this->getCountry($id);
        $Country->update($data);
        return $Country;
    }

    /**
     * @param int $id
     * @return Country
     * @throws \Exception
     */
    public function deleteCountry(int $id): Country
    {
        $Country = $this->getCountry($id);
        $Country->delete();
        return $Country;
    }

    /**
     * @param array $data
     * @return Country|mixed
     */
    public function importRecord(array $data)
    {
        if ($this->model->where('name', $data['name'])->get()->isEmpty()) {
            return $this->storeCountry($data);
        }
    }
}
