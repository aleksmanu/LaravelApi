<?php
namespace App\Modules\Client\Repositories;

use App\Modules\Client\Models\OrganisationType;
use App\Modules\Core\Interfaces\IYardiImport;
use Illuminate\Support\Collection;

class OrganisationTypeRepository implements IYardiImport
{
    /**
     * @var OrganisationType
     */
    protected $model;

    /**
     * OrganisationTypeRepository constructor.
     * @param OrganisationType $model
     */
    public function __construct(OrganisationType $model)
    {
        $this->model = $model;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function getOrganisationTypes(): Collection
    {
        return $this->model->orderBy('name', 'asc')->get();
    }

    /**
     * @param $id
     * @return OrganisationType
     */
    public function getOrganisationType($id): OrganisationType
    {
        return $this->model->findOrFail($id);
    }

    /**
     * @param array $data
     * @return OrganisationType
     */
    public function storeOrganisationType(array $data): OrganisationType
    {
        return $this->model->create($data);
    }

    /**
     * @param int $id
     * @param array $data
     * @return OrganisationType
     */
    public function updateOrganisationType(int $id, array $data): OrganisationType
    {
        $organisationType = $this->getOrganisationType($id);
        $organisationType->update($data);

        return $organisationType;
    }

    /**
     * @param array $data
     * @return OrganisationType|mixed
     */
    public function importRecord(array $data)
    {
        if ($this->model->where('name', $data['name'])->get()->isEmpty()) {
            return $this->storeOrganisationType($data);
        }
    }
}
