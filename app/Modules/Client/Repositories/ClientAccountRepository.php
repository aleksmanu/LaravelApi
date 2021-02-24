<?php

namespace App\Modules\Client\Repositories;

use App\Modules\Account\Models\AccountType;
use App\Modules\Account\Repositories\AccountRepository;
use App\Modules\Auth\Models\User;
use App\Modules\Client\Models\ClientAccount;
use App\Modules\Client\Models\ClientAccountStatus;
use App\Modules\Client\Models\OrganisationType;
use App\Modules\Core\Classes\Repository;
use App\Modules\Core\Interfaces\IYardiImport;
use App\Modules\Core\Library\EloquentHelper;
use App\Modules\Edits\Models\ReviewStatus;
use App\Modules\Property\Models\PropertyManager;
use App\Modules\Common\Traits\HasServerSideSortingTrait;
use Illuminate\Support\Collection;
use App\Modules\Attachments\Traits\RepoHasAttachments;
use App\Modules\Common\Repositories\AddressRepository;

class ClientAccountRepository extends Repository implements IYardiImport
{
    use HasServerSideSortingTrait, RepoHasAttachments;

    private $import = false;

    /**
     * ClientAccountRepository constructor.
     * @param ClientAccount $model
     */
    public function __construct(ClientAccount $model)
    {
        $this->model = $model;
    }

    public function getImport()
    {
        return $this->import;
    }

    /**
     * @param $property_manager_id
     * @param $client_account_status_id
     * @param $org_type_id
     * @return Collection
     */
    public function getClientAccounts($property_manager_id, $client_account_status_id, $org_type_id): Collection
    {

        $query = $this->model
            ->select($this->model->getTableName() . '.*')
            ->orderBy($this->model->getTableName() . '.name', 'asc');
        $query = $this->applyFilters($query, $property_manager_id, $client_account_status_id, $org_type_id);

        return $query->get();
    }

    /**
     * @param array $params
     * @return Collection
     */
    public function identifyClientAccounts(array $params): Collection
    {
        $query = $this->model
            ->newQueryWithoutRelationships()
            ->select($this->model->getTableName() . '.id');

        if (isset($params['client_account_id'])) {
            $params['id'] = $params['client_account_id'];
        }

        foreach ($params as $key => $val) {
            if ($key === "id" || in_array($key, $this->model->fillable)) {
                $query->where($this->model->getTableName() . '.' . $key, $val);
            }
        }

        return $query->get()->pluck('id');
    }

    /**
     * @param array $ids
     * @return Collection
     */
    public function findClientAccounts(array $ids): Collection
    {
        return $this->model->newQueryWithoutRelationships()
            ->select($this->model->getTableName() . '.id', $this->model->getTableName() . '.name', $this->model->getTableName() . '.yardi_client_ref', $this->model->getTableName() . '.yardi_client_ref')
            ->withCount($this->model->withCount)
            ->whereIn($this->model->getTableName() . '.id', $ids)
            ->orderBy($this->model->getTableName() . '.name', 'asc')
            ->get();
    }

    /**
     * @param $sort_column
     * @param $sort_order
     * @param $offset
     * @param $limit
     * @param $property_manager_id
     * @param $client_account_status_id
     * @param $org_type_id
     * @param $client_account_name_partial
     * @return Collection
     */
    public function getClientAccountDataTable(
        $sort_column,
        $sort_order,
        $offset,
        $limit,
        $property_manager_id,
        $client_account_status_id,
        $org_type_id,
        $client_account_name_partial
    ): Collection {
        $query  = $this->model
            ->select($this->model->getTableName() . '.id', $this->model->getTableName() . '.name', $this->model->getTableName() . '.yardi_client_ref', $this->model->getTableName() . '.yardi_alt_ref', $this->model->getTableName() . '.client_account_status_id', $this->model->getTableName() . '.property_manager_id', $this->model->getTableName() . '.organisation_type_id')
            ->with([
                'clientAccountStatus:id,name',
                'propertyManager' => function ($query) {
                    $query->select('id', 'user_id')->without('realUnits', 'units');
                },
                'propertyManager.user' => function ($query) {
                    $query->select('id', 'first_name', 'last_name');
                },
                'organisationType:id,name'
            ])
            ->withCount($this->model->withCount)
            ->groupBy($this->model->getTableName() . '.id');

        $query = $this->applyFilters(
            $query,
            $property_manager_id,
            $client_account_status_id,
            $org_type_id,
            $client_account_name_partial
        );

        // Get a copy of the query at this point with no limits applied, otherwise total count will be skewed
        // Insert the monster query inside a simple count() query. Replace bindings first
        $countSelect = 'SELECT count(*) as row_count FROM (' . $query->toSql() . ') AS T1';
        $bindParams  = $query->getBindings(); //keep a hold of these before sort and limit is added

        $this->johnifySortColumn($sort_column);

        $query->orderBy($sort_column, $sort_order)
            ->skip($offset)
            ->take($limit);

        $clientAccounts = $query->get();
        foreach ($clientAccounts as $clientAccount) {
            $clientAccount->setAppends(['real_units_count']);
        }

        return collect([
            'row_count' => \DB::select($countSelect, $bindParams)[0]->row_count,
            'rows'      => $clientAccounts
        ]);
    }

    /**
     * @param $property_manager_id
     * @param $client_account_status_id
     * @param $org_type_id
     * @param $query
     * @param $client_account_name_partial
     * @return mixed
     */
    private function applyFilters(
        $query,
        $property_manager_id,
        $client_account_status_id,
        $org_type_id,
        $client_account_name_partial = ''
    ) {

        if ($property_manager_id) {
            $query->where($this->model->getTableName() . '.property_manager_id', $property_manager_id);
        }

        if ($client_account_status_id) {
            $query->where($this->model->getTableName() . '.client_account_status_id', $client_account_status_id);
        }

        if ($org_type_id) {
            $query->where($this->model->getTableName() . '.organisation_type_id', $org_type_id);
        }

        if ($client_account_name_partial) {
            $query->where($this->model->getTableName() . '.name', 'LIKE', '%' . $client_account_name_partial . '%');
        }

        return $query;
    }

    /**
     * @param int $id
     * @return ClientAccount
     */
    public function getClientAccount(int $id): ClientAccount
    {
        return $this->model
            ->select($this->model->getTableName() . '.*')
            ->with([
                'propertyManager.user',
                'clientAccountStatus',
                'organisationType',
                'address',
                'portfolios',
                'reviewStatus',
                'lockedByUser'
            ])
            ->findOrFail($id);
    }

    /**
     * @param array $data
     * @return ClientAccount
     */
    public function storeClientAccount(array $data): ClientAccount
    {

        $data['review_status_id'] = EloquentHelper::getRecordIdBySlug(
            ReviewStatus::class,
            ReviewStatus::NEVER_REVIEWED
        );

        return $this->model->create($data);
    }

    /**
     * @param int $id
     * @param array $data
     * @return ClientAccount
     */
    public function updateClientAccount(int $id, array $data): ClientAccount
    {
        $clientAccount = $this->getClientAccount($id);
        $clientAccount->update($data);
        return $clientAccount;
    }

    /**
     * @param int $id
     * @return ClientAccount
     * @throws \Exception
     */
    public function deleteClientAccount(int $id): ClientAccount
    {
        $clientAccount = $this->getClientAccount($id);
        $clientAccount->delete();
        $clientAccount->account->delete();
        return $clientAccount;
    }

    /**
     * @param array $data
     * @return ClientAccount
     */
    public function importRecord(array $data)
    {
        $this->import = true;
        $account_repository = \App::make(AccountRepository::class);

        $id = $this->identifyClientAccounts(['yardi_client_ref' => $data['client_ref']])->first();
        if ($id) {
            $client_account = $this->getClientAccount($id);
            $account = $client_account->account;
        } else {
            $account = $account_repository->storeAccount([
                'account_type_id' => EloquentHelper::getRecordIdBySlug(AccountType::class, AccountType::CLIENT)
            ]);
        }

        $account->update(['name' => $data['client_name']]);

        $org_type = OrganisationType::where('name', $data['org_type'])->first();
        $client_status = ClientAccountStatus::where('name', $data['status'])->first();
        $property_manager = User::where('first_name', $data['first_name'])
            ->where('last_name', $data['last_name'])
            ->first()->propertyManager;

        $address_repository = \App::make(AddressRepository::class);
        if ($id) {
            $address = $address_repository->importRecord($data, $client_account->address->id);
        } else {
            $address = $address_repository->importRecord($data);
        }

        $databaseableData = [
            'account_id'               => $account->id,
            'organisation_type_id'     => $org_type ? $org_type->id : null,
            'address_id'               => $address->id,
            'client_account_status_id' => $client_status->id,
            'property_manager_id'      => $property_manager->id,
            'name'                     => $data['client_name'],
            'yardi_client_ref'         => $data['client_ref'],
            'yardi_alt_ref'            => $data['alt_ref'],
        ];

        if ($id) {
            $client_account->update($databaseableData);
        } else {
            return $this->storeClientAccount($databaseableData);
        }
    }
}
