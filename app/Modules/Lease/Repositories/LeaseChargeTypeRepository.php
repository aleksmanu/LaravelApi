<?php
namespace App\Modules\Lease\Repositories;

use App\Modules\Lease\Models\LeaseChargeType;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;

class LeaseChargeTypeRepository
{
    /**
     * @var LeaseChargeType
     */
    protected $model;

    /**
     * LeaseChargeTypeRepository constructor.
     * @param LeaseChargeType $model
     */
    public function __construct(LeaseChargeType $model)
    {
        $this->model = $model;
    }

    /**
     * @return Collection
     */
    public function getLeaseChargeTypes(): Collection
    {
        return $this->model->orderBy('name', 'asc')->get();
    }

    /**
     * @param $id
     * @return LeaseChargeType
     */
    public function getLeaseChargeType(int $id): LeaseChargeType
    {
        return $this->model->findOrFail($id);
    }

    /**
     * @param array $data
     * @return LeaseChargeType
     */
    public function storeLeaseChargeType(array $data): LeaseChargeType
    {
        $data['slug'] = $this->generateSlug($data['name']);
        return $this->model->create($data);
    }

    /**
     * @param int $id
     * @param array $data
     * @return LeaseChargeType
     */
    public function updateLeaseChargeType(int $id, array $data): LeaseChargeType
    {
        $LeaseChargeType = $this->getLeaseChargeType($id);
        $data['slug'] = $this->generateSlug($data['name']);
        $LeaseChargeType->update($data);
        return $LeaseChargeType;
    }

    /**
     * @param int $id
     * @return LeaseChargeType
     * @throws \Exception
     */
    public function deleteLeaseChargeType(int $id): LeaseChargeType
    {
        $LeaseChargeType = $this->getLeaseChargeType($id);
        $LeaseChargeType->delete();
        return $LeaseChargeType;
    }

    /** Generates a unique slug by appending incrementing numbers to the end if duplicates exist
     * @param string $name
     * @return string
     */
    public function generateSlug(string $name): string
    {
        $slug = str_slug($name);

        if (LeaseChargeType::where('slug', $slug)->exists()) {
            $suffix = 1;
            while (($slug = str_slug($name . '-' . $suffix)) && LeaseChargeType::where('slug', $slug)->exists()) {
                $suffix++;
            }
        }

        return $slug;
    }

    public function importRecord(array $data)
    {
        try {
            $type = $this->model->findOrFail($data['id']);
            $type->name = $data['name'];
            $type->save();
        } catch (ModelNotFoundException $e) {
            return $this->storeLeaseChargeType($data);
        }
    }
}
