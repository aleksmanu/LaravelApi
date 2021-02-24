<?php
namespace App\Modules\Property\Repositories;

use App\Modules\Core\Interfaces\IYardiImport;
use App\Modules\Property\Models\Partner;
use Illuminate\Support\Collection;

class PartnerRepository implements IYardiImport
{
    /**
     * @var Partner
     */
    protected $model;

    /**
     * PartnerRepository constructor.
     * @param Partner $model
     */
    public function __construct(Partner $model)
    {
        $this->model = $model;
    }

    /**
     * @return Collection
     */
    public function getPartners(): Collection
    {
        return $this->model->orderBy('name', 'asc')->get();
    }

    /**
     * @param $id
     * @return Partner
     */
    public function getPartner($id): Partner
    {
        return $this->model->findOrFail($id);
    }

    /**
     * @param $id
     * @return Partner
     */
    public function getPropertiesForPartner($id): Partner
    {
        return $this->model->with('properties')->findOrFail($id);
    }

    /**
     * @return Partner
     */
    public function getPropertiesForPartners(): Collection
    {
        return $this->model->with('properties')->orderBy('name', 'asc')->get();
    }

    /**
     * @param array $data
     * @return Partner|mixed
     */
    public function importRecord(array $data)
    {
        return $this->storePartner($data);
    }
}
