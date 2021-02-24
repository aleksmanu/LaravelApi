<?php
namespace App\Modules\Lease\Repositories;

use App\Modules\Core\Interfaces\IYardiImport;
use App\Modules\Lease\Models\BreakPartyOption;
use Illuminate\Support\Collection;

class BreakPartyOptionRepository implements IYardiImport
{
    /**
     * @var BreakPartyOption
     */
    protected $model;

    /**
     * BreakPartyOptionRepository constructor.
     * @param BreakPartyOption $model
     */
    public function __construct(BreakPartyOption $model)
    {
        $this->model = $model;
    }

    /**
     * @return Collection
     */
    public function getBreakPartyOptions(): Collection
    {
        return $this->model->orderBy('name', 'asc')->get();
    }

    /**
     * @param $id
     * @return BreakPartyOption
     */
    public function getBreakPartyOption($id): BreakPartyOption
    {
        return $this->model->findOrFail($id);
    }

    /**
     * @param array $data
     * @return BreakPartyOption
     */
    public function storeBreakPartyOption(array $data): BreakPartyOption
    {
        $data['slug'] = $this->generateSlug($data['name']);
        return $this->model->create($data);
    }

    /**
     * @param int $id
     * @param array $data
     * @return BreakPartyOption
     */
    public function updateBreakPartyOption(int $id, array $data): BreakPartyOption
    {
        $BreakPartyOption = $this->getBreakPartyOption($id);
        $data['slug'] = $this->generateSlug($data['name']);
        $BreakPartyOption->update($data);
        return $BreakPartyOption;
    }

    /**
     * @param int $id
     * @return BreakPartyOption
     * @throws \Exception
     */
    public function deleteBreakPartyOption(int $id): BreakPartyOption
    {
        $BreakPartyOption = $this->getBreakPartyOption($id);
        $BreakPartyOption->delete();
        return $BreakPartyOption;
    }

    /** Generates a unique slug by appending incrementing numbers to the end if duplicates exist
     * @param string $name
     * @return string
     */
    public function generateSlug(string $name): string
    {
        $slug = str_slug($name);

        if (BreakPartyOption::where('slug', $slug)->exists()) {
            $suffix = 1;
            while (($slug = str_slug($name . '-' . $suffix)) && BreakPartyOption::where('slug', $slug)->exists()) {
                $suffix++;
            }
        }

        return $slug;
    }

    /**
     * @param array $data
     * @return BreakPartyOption|mixed
     */
    public function importRecord(array $data)
    {
        if ($this->model->where('name', $data['name'])->get()->isEmpty()) {
            return $this->storeBreakPartyOption($data);
        }
    }
}
