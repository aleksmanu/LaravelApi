<?php
namespace App\Modules\Property\Repositories;

use App\Modules\Core\Interfaces\IYardiImport;
use App\Modules\Property\Models\LocationType;
use Illuminate\Support\Collection;

class LocationTypeRepository implements IYardiImport
{
    /**
     * @var LocationType
     */
    protected $model;

    /**
     * LocationTypeRepository constructor.
     * @param LocationType $model
     */
    public function __construct(LocationType $model)
    {
        $this->model = $model;
    }

    /**
     * @return Collection
     */
    public function getLocationTypes(): Collection
    {
        return $this->model->orderBy('name', 'asc')->get();
    }

    /**
     * @param int $id
     * @return LocationType
     */
    public function getLocationType(int $id): LocationType
    {
        return $this->model->findOrFail($id);
    }

    /**
     * @param array $data
     * @return LocationType
     */
    public function storeLocationType(array $data): LocationType
    {
        $data['slug'] = $this->generateSlug($data['name']);
        return $this->model->create($data);
    }

    /**
     * @param int $id
     * @param array $data
     * @return LocationType
     */
    public function updateLocationType(int $id, array $data): LocationType
    {
        $LocationType = $this->getLocationType($id);
        $data['slug'] = $this->generateSlug($data['name']);
        $LocationType->update($data);
        return $LocationType;
    }

    /**
     * @param int $id
     * @return LocationType
     * @throws \Exception
     */
    public function deleteLocationType(int $id): LocationType
    {
        $LocationType = $this->getLocationType($id);
        $LocationType->delete();
        return $LocationType;
    }

    /** Generates a unique slug by appending incrementing numbers to the end if duplicates exist
     * @param string $name
     * @return string
     */
    public function generateSlug(string $name): string
    {
        $slug = str_slug($name);

        if (LocationType::where('slug', $slug)->exists()) {
            $suffix = 1;
            while (($slug = str_slug($name . '-' . $suffix)) && LocationType::where('slug', $slug)->exists()) {
                $suffix++;
            }
        }

        return $slug;
    }

    /**
     * @param array $data
     * @return LocationType|mixed
     */
    public function importRecord(array $data)
    {
        if ($this->model->where('name', $data['name'])->get()->isEmpty()) {
            return $this->storeLocationType($data);
        }
    }
}
