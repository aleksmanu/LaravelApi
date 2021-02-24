<?php
namespace App\Modules\Lease\Repositories;

use App\Modules\Core\Interfaces\IYardiImport;
use App\Modules\Lease\Models\ReviewType;
use Illuminate\Support\Collection;

class ReviewTypeRepository implements IYardiImport
{
    /**
     * @var ReviewType
     */
    protected $model;

    /**
     * ReviewTypeRepository constructor.
     * @param ReviewType $model
     */
    public function __construct(ReviewType $model)
    {
        $this->model = $model;
    }

    /**
     * @return Collection
     */
    public function getReviewTypes(): Collection
    {
        return $this->model
            ->select($this->model->getTableName() . '.*')
            ->orderBy($this->model->getTableName() . '.name', 'asc')
            ->groupBy($this->model->getTableName() . '.id')
            ->get();
    }

    /**
     * @param int $id
     * @return ReviewType
     */
    public function getReviewType(int $id): ReviewType
    {
        return $this->model
            ->select($this->model->getTableName() . '.*')
            ->groupBy($this->model->getTableName() . '.id')
            ->findOrFail($id);
    }

    /**
     * @param array $data
     * @return ReviewType
     */
    public function storeReviewType(array $data): ReviewType
    {
        $data['slug'] = $this->generateSlug($data['name']);
        return $this->model->create($data);
    }

    /**
     * @param int $id
     * @param array $data
     * @return ReviewType
     */
    public function updateReviewType(int $id, array $data): ReviewType
    {
        $ReviewType = $this->getReviewType($id);
        $data['slug'] = $this->generateSlug($data['name']);
        $ReviewType->update($data);
        return $ReviewType;
    }

    /**
     * @param int $id
     * @return ReviewType
     * @throws \Exception
     */
    public function deleteReviewType(int $id): ReviewType
    {
        $ReviewType = $this->getReviewType($id);
        $ReviewType->delete();
        return $ReviewType;
    }

    /** Generates a unique slug by appending incrementing numbers to the end if duplicates exist
     * @param string $name
     * @return string
     */
    public function generateSlug(string $name): string
    {
        $slug = str_slug($name);

        if (ReviewType::where('slug', $slug)->exists()) {
            $suffix = 1;
            while (($slug = str_slug($name . '-' . $suffix)) && ReviewType::where('slug', $slug)->exists()) {
                $suffix++;
            }
        }

        return $slug;
    }

    /**
     * @param array $data
     * @return ReviewType|mixed
     */
    public function importRecord(array $data)
    {
        if ($this->model->where('name', $data['name'])->get()->isEmpty()) {
            return $this->storeReviewType($data);
        }
    }
}
