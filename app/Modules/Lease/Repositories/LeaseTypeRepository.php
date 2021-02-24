<?php
namespace App\Modules\Lease\Repositories;

use App\Modules\Core\Interfaces\IYardiImport;
use App\Modules\Lease\Models\LeaseType;
use Illuminate\Support\Collection;

class LeaseTypeRepository implements IYardiImport
{
    /**
     * @var LeaseType
     */
    protected $model;

    /**
     * LeaseTypeRepository constructor.
     * @param LeaseType $model
     */
    public function __construct(LeaseType $model)
    {
        $this->model = $model;
    }

    /**
     * @return Collection
     */
    public function getLeaseTypes(): Collection
    {
        return $this->model->orderBy('name', 'asc')->get();
    }

    /**
     * @param $id
     * @return LeaseType
     */
    public function getLeaseType(int $id): LeaseType
    {
        return $this->model->findOrFail($id);
    }

    /**
     * @param array $data
     * @return LeaseType
     */
    public function storeLeaseType(array $data): LeaseType
    {
        $data['slug'] = $this->generateSlug($data['name']);
        return $this->model->create($data);
    }

    /**
     * @param int $id
     * @param array $data
     * @return LeaseType
     */
    public function updateLeaseType(int $id, array $data): LeaseType
    {
        $LeaseType = $this->getLeaseType($id);
        $data['slug'] = $this->generateSlug($data['name']);
        $LeaseType->update($data);
        return $LeaseType;
    }

    /**
     * @param int $id
     * @return LeaseType
     * @throws \Exception
     */
    public function deleteLeaseType(int $id): LeaseType
    {
        $LeaseType = $this->getLeaseType($id);
        $LeaseType->delete();
        return $LeaseType;
    }

    /** Generates a unique slug by appending incrementing numbers to the end if duplicates exist
     * @param string $name
     * @return string
     */
    public function generateSlug(string $name): string
    {
        $slug = str_slug($name);

        if (LeaseType::where('slug', $slug)->exists()) {
            $suffix = 1;
            while (($slug = str_slug($name . '-' . $suffix)) && LeaseType::where('slug', $slug)->exists()) {
                $suffix++;
            }
        }

        return $slug;
    }

    /**
     * @param array $data
     * @return LeaseType|mixed
     */
    public function importRecord(array $data)
    {
        if ($this->model->where('name', $data['name'])->get()->isEmpty()) {
            return $this->storeLeaseType($data);
        }
    }
}
