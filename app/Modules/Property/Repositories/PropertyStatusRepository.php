<?php
namespace App\Modules\Property\Repositories;

use App\Modules\Core\Interfaces\IYardiImport;
use App\Modules\Property\Models\PropertyStatus;
use Illuminate\Support\Collection;

class PropertyStatusRepository implements IYardiImport
{
    /**
     * @var PropertyStatus
     */
    protected $model;

    /**
     * PropertyStatusRepository constructor.
     * @param PropertyStatus $model
     */
    public function __construct(PropertyStatus $model)
    {
        $this->model = $model;
    }

    /**
     * @return Collection
     */
    public function getPropertyStatuses(): Collection
    {
        return $this->model
            ->select($this->model->getTableName() . '.*')
            ->orderBy($this->model->getTableName() . '.name', 'asc')
            ->groupBy($this->model->getTableName() . '.id', $this->model->getTableName() . '.name')
            ->get();
    }

    /**
     * @param int $id
     * @return PropertyStatus
     */
    public function getPropertyStatus(int $id): PropertyStatus
    {
        return $this->model
            ->select($this->model->getTableName() . '.*')
            ->groupBy($this->model->getTableName() . '.id')
            ->findOrFail($id);
    }

    /**
     * @param array $data
     * @return PropertyStatus
     */
    public function storePropertyStatus(array $data): PropertyStatus
    {
        $data['slug'] = $this->generateSlug($data['name']);
        return $this->model->create($data);
    }

    /**
     * @param int $id
     * @param array $data
     * @return PropertyStatus
     */
    public function updatePropertyStatus(int $id, array $data): PropertyStatus
    {
        $PropertyStatus = $this->getPropertyStatus($id);
        $data['slug'] = $this->generateSlug($data['name']);
        $PropertyStatus->update($data);
        return $PropertyStatus;
    }

    /**
     * @param int $id
     * @return PropertyStatus
     * @throws \Exception
     */
    public function deletePropertyStatus(int $id): PropertyStatus
    {
        $PropertyStatus = $this->getPropertyStatus($id);
        $PropertyStatus->delete();
        return $PropertyStatus;
    }

    /** Generates a unique slug by appending incrementing numbers to the end if duplicates exist
     * @param string $name
     * @return string
     */
    public function generateSlug(string $name): string
    {
        $slug = str_slug($name);

        if (PropertyStatus::where('slug', $slug)->exists()) {
            $suffix = 1;
            while (($slug = str_slug($name . '-' . $suffix)) && PropertyStatus::where('slug', $slug)->exists()) {
                $suffix++;
            }
        }

        return $slug;
    }

    /**
     * @param string $slug
     * @return string
     */
    public function findSlug(string $slug): PropertyStatus
    {
        return PropertyStatus::where('property_statuses.slug', $slug)->first();
    }

    /**
     * @param array $data
     * @return PropertyStatus
     */
    public function importRecord(array $data)
    {
        if ($this->model->where('name', $data['name'])->get()->isEmpty()) {
            return $this->storePropertyStatus($data);
        }
    }
}
