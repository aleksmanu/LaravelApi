<?php
namespace App\Modules\Lease\Repositories;

use App\Modules\Core\Interfaces\IYardiImport;
use App\Modules\Lease\Models\TransactionType;
use Illuminate\Support\Collection;

class TransactionTypeRepository implements IYardiImport
{

    /**
     * @var TransactionType
     */
    protected $model;

    /**
     * TransactionTypeRepository constructor.
     * @param TransactionType $model
     */
    public function __construct(TransactionType $model)
    {
        $this->model = $model;
    }

    /**
     * @return Collection
     */
    public function getTransactionTypes(): Collection
    {
        return $this->model->orderBy('name', 'asc')->get();
    }

    /**
     * @param $id
     * @return TransactionType
     */
    public function getTransactionType(int $id): TransactionType
    {
        return $this->model->findOrFail($id);
    }

    /**
     * @param array $data
     * @return TransactionType
     */
    public function storeTransactionType(array $data): TransactionType
    {
        return $this->model->create($data);
    }

    /**
     * @param int $id
     * @param array $data
     * @return TransactionType
     */
    public function updateTransactionType(int $id, array $data): TransactionType
    {
        $TransactionType = $this->getTransactionType($id);
        $TransactionType->update($data);
        return $TransactionType;
    }

    /**
     * @param int $id
     * @return TransactionType
     * @throws \Exception
     */
    public function deleteTransactionType(int $id): TransactionType
    {
        $TransactionType = $this->getTransactionType($id);
        $TransactionType->delete();
        return $TransactionType;
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function importRecord(array $data)
    {
        if ($this->model->where('code', $data['code'])->get()->isEmpty()) {
            return $this->storeTransactionType($data);
        }
    }
}
