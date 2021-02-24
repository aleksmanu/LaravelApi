<?php
namespace App\Modules\Account\Repositories;

use App\Modules\Account\Models\Account;
use Illuminate\Support\Collection;

class AccountRepository
{

    /**
     * @var Account
     */
    protected $model;

    /**
     * AccountRepository constructor.
     * @param Account $model
     */
    public function __construct(Account $model)
    {
        $this->model = $model;
    }

    /**
     * @param $account_type_id
     * @return Collection
     */
    public function getAccounts($account_type_id): Collection
    {

        $query = $this->model->orderBy($this->model->getTableName() . '.name', 'asc');

        if ($account_type_id) {
            $query->where('account_type_id', $account_type_id);
        }

        return $query->get();
    }

    /**
     * @param int $id
     * @return Account
     */
    public function getAccount(int $id): Account
    {
        return $this->model->findOrFail($id);
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function storeAccount(array $data)
    {
        return $this->model->create($data);
    }

    /**
     * @param int $id
     * @param array $data
     * @return Account|mixed
     */
    public function updateAccount(int $id, array $data)
    {
        $account = $this->getAccount($id);
        $account->update($data);
        return $account;
    }
}
