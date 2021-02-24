<?php
namespace App\Modules\Account\Repositories;

use App\Modules\Account\Models\AccountType;
use Illuminate\Support\Collection;

class AccountTypeRepository
{

    /**
     * @var AccountType
     */
    protected $model;

    /**
     * AccountTypeRepository constructor.
     * @param AccountType $model
     */
    public function __construct(AccountType $model)
    {
        $this->model = $model;
    }

    /**
     * @return Collection
     */
    public function getAccountTypes(): Collection
    {
        return $this->model->orderBy('name', 'asc')->get();
    }
}
