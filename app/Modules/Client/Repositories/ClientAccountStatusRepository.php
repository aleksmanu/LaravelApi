<?php
namespace App\Modules\Client\Repositories;

use App\Modules\Client\Models\ClientAccountStatus;
use App\Modules\Core\Interfaces\IYardiImport;
use Illuminate\Support\Collection;

class ClientAccountStatusRepository implements IYardiImport
{

    /**
     * @var ClientAccountStatus
     */
    protected $model;

    /**
     * ClientAccountStatusRepository constructor.
     * @param ClientAccountStatus $model
     */
    public function __construct(ClientAccountStatus $model)
    {
        $this->model = $model;
    }

    /**
     * @return Collection
     */
    public function getClientAccountStatuses(): Collection
    {
        return $this->model->orderBy('name', 'asc')->get();
    }

    /**
     * @param int $id
     * @return ClientAccountStatus
     */
    public function getClientAccountStatus(int $id): ClientAccountStatus
    {
        return $this->model->findOrFail($id);
    }

    /**
     * @param array $data
     * @return ClientAccountStatus
     */
    public function storeClientAccountStatus(array $data): ClientAccountStatus
    {
        $data['slug'] = $this->generateSlug($data['name']);
        return $this->model->create($data);
    }

    /**
     * @param int $id
     * @param array $data
     * @return ClientAccountStatus
     */
    public function updateClientAccountStatus(int $id, array $data): ClientAccountStatus
    {
        $clientAccountStatus = $this->getClientAccountStatus($id);
        $data['slug'] = $this->generateSlug($data['name']);
        $clientAccountStatus->update($data);
        return $clientAccountStatus;
    }

    /**
     * @param int $id
     * @return ClientAccountStatus
     * @throws \Exception
     */
    public function deleteClientAccountStatus(int $id): ClientAccountStatus
    {
        $clientAccountStatus = $this->getClientAccountStatus($id);
        $clientAccountStatus->delete();
        return $clientAccountStatus;
    }

    /** Generates a unique slug by appending incrementing numbers to the end if duplicates exist
     * @param string $name
     * @return string
     */
    public function generateSlug(string $name): string
    {
        $slug = str_slug($name);

        if (ClientAccountStatus::where('slug', $slug)->exists()) {
            $suffix = 1;
            while (($slug = str_slug($name . '-' . $suffix)) && ClientAccountStatus::where('slug', $slug)->exists()) {
                $suffix++;
            }
        }

        return $slug;
    }

    /**
     * @param array $data
     * @return ClientAccountStatus|mixed
     */
    public function importRecord(array $data)
    {
        if ($this->model->where('name', $data['name'])->get()->isEmpty()) {
            return $this->storeClientAccountStatus($data);
        }
    }
}
