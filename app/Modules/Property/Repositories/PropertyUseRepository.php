<?php
namespace App\Modules\Property\Repositories;

use App\Modules\Core\Interfaces\IYardiImport;
use App\Modules\Property\Models\PropertyUse;
use Illuminate\Support\Collection;

class PropertyUseRepository implements IYardiImport
{
    /**
     * @var PropertyUse
     */
    protected $model;

    /**
     * PropertyUseRepository constructor.
     * @param PropertyUse $model
     */
    public function __construct(PropertyUse $model)
    {
        $this->model = $model;
    }

    /**
     * @return Collection
     */
    public function getPropertyUses(): Collection
    {
        return $this->model
            ->select($this->model->getTableName() . '.*')
            ->orderBy($this->model->getTableName() . '.name', 'asc')
            ->groupBy($this->model->getTableName() . '.id')
            ->get();
    }

    /**
     * @param int $id
     * @return PropertyUse
     */
    public function getPropertyUse(int $id): PropertyUse
    {
        return $this->model
            ->select($this->model->getTableName() . '.*')
            ->orderBy($this->model->getTableName() . '.name', 'asc')
            ->groupBy($this->model->getTableName() . '.id')
            ->findOrFail($id);
    }

    /**
     * @param array $data
     * @return PropertyUse
     */
    public function storePropertyUse(array $data): PropertyUse
    {
        $data['slug'] = $this->generateSlug($data['name']);
        return $this->model->create($data);
    }

    /**
     * @param int $id
     * @param array $data
     * @return PropertyUse
     */
    public function updatePropertyUse(int $id, array $data): PropertyUse
    {
        $propertyUse = $this->getPropertyUse($id);
        $data['slug'] = $this->generateSlug($data['name']);
        $propertyUse->update($data);
        return $propertyUse;
    }

    /**
     * @param int $id
     * @return PropertyUse
     * @throws \Exception
     */
    public function deletePropertyUse(int $id): PropertyUse
    {
        $propertyUse = $this->getPropertyUse($id);
        $propertyUse->delete();
        return $propertyUse;
    }

    /** Generates a unique slug by appending incrementing numbers to the end if duplicates exist
     * @param string $name
     * @return string
     */
    public function generateSlug(string $name): string
    {
        $slug = str_slug($name);

        if (PropertyUse::where('slug', $slug)->exists()) {
            $suffix = 1;
            while (($slug = str_slug($name . '-' . $suffix)) && PropertyUse::where('slug', $slug)->exists()) {
                $suffix++;
            }
        }

        return $slug;
    }

    /**
     * @param array $data
     * @return PropertyUse|mixed
     */
    public function importRecord(array $data)
    {
        if ($this->model->where('name', $data['name'])->get()->isEmpty()) {
            return $this->storePropertyUse($data);
        }
    }
}
