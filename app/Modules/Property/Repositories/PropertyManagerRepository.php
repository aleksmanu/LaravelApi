<?php

namespace App\Modules\Property\Repositories;

use Illuminate\Support\Collection;
use App\Modules\Account\Models\Account;
use App\Modules\Auth\Models\Role;
use App\Modules\Auth\Models\User;
use App\Modules\Auth\Repositories\Eloquent\UserRepository;
use App\Modules\Core\Interfaces\IYardiImport;
use App\Modules\Core\Library\AuthHelper;
use App\Modules\Property\Models\PropertyManager;

class PropertyManagerRepository implements IYardiImport
{
    /**
     * @var PropertyManager
     */
    protected $model;

    /**
     * PropertyManagerRepository constructor.
     * @param PropertyManager $model
     */
    public function __construct(PropertyManager $model)
    {
        $this->model = $model;
    }

    /**
     * @return Array
     */
    public function getPropertyManagers()
    {
        return $this->model->newQueryWithoutRelationships()
            ->select($this->model->getTableName() . '.*')
            ->with(['user'])->get()->sortBy('user.first_name')->values()->all();
    }

    /**
     * @param array $ids
     * @return Array
     */
    public function findPropertyManagers(array $ids): array
    {
        return $this->model
            ->whereIn($this->model->getTableName() . '.id', $ids)
            ->with(['user' => function ($query) {
                $query->select('id', 'first_name', 'last_name');
            }])
            ->without('units')
            ->get()
            ->sortBy('user.first_name')->values()->all();
    }

    /**
     * @param int $id
     * @return PropertyManager
     */
    public function getPropertyManager(int $id): PropertyManager
    {
        return $this->model
            ->select($this->model->getTableName() . '.*')
            ->findOrFail($id);
    }

    /**
     * @param array $data
     * @return PropertyManager
     */
    public function storePropertyManager(array $data): PropertyManager
    {
        return $this->model->create($data);
    }

    /**
     * @param int $id
     * @param array $data
     * @return PropertyManager
     */
    public function updatePropertyManager(int $id, array $data): PropertyManager
    {
        $propertyManager = $this->getPropertyManager($id);
        $propertyManager->update($data);
        return $propertyManager;
    }

    /**
     * @param int $id
     * @return PropertyManager
     * @throws \Exception
     */
    public function deletePropertyManager(int $id): PropertyManager
    {
        $propertyManager = $this->getPropertyManager($id);
        $propertyManager->delete();
        return $propertyManager;
    }

    public function getPropertyManagersDataTable(
        $sort_attr = 'id',
        $sort_dir = 'asc',
        $page_limit = null,
        $offset = null,
        $client_id = null
    ): Collection {
        $query = $this->model->select($this->model->getTableName() . '.*'); // TODO replace with filtered query

        $query->orderBy($sort_attr, $sort_dir) // TODO: re-enable once front-end is upgraded
            //->skip($offset)
            //->take($page_limit)
            ->with(['user'])
            ->withCount(['clientAccounts', 'properties', 'units', 'realUnits']);

        $propertyManagers = $query->get();

        $data = [];

        foreach ($propertyManagers as $propertyManager) {
            array_push($data, [
                'id'                    => $propertyManager->id,
                'first_name'            => $propertyManager->user->first_name,
                'last_name'             => $propertyManager->user->last_name,
                'email'                 => $propertyManager->user->email,
                'client_accounts_count' => $propertyManager->client_accounts_count,
                'properties_count'      => $propertyManager->properties_count,
                'units_count'           => $propertyManager->units_count,
                'real_units_count'      => $propertyManager->real_units_count,
            ]);
        }

        return collect($data);
    }

    /** TODO
     * @return Builder
     */
    private function getBaseQuery()
    {
        return $this->model->join('users', 'users.id', '=', 'property_managers.user_id')
            ->select('users.*');
    }

    /**
     * @param $query
     * @param int $client_id
     * @return mixed
     */
    private function applyFilters($query, $client_id = 0)
    {

        if ($client_id) {
            $query->where('property_managers.account_type_id', $client_id);
        }

        return $query;
    }

    /**
     * @param array $data
     * @return PropertyManager|mixed
     */
    public function importRecord(array $data)
    {
        $user_repository = \App::make(UserRepository::class);
        $user = User::withTrashed()->where('email', $data['email'])->first();
        $userData = [
            'first_name' => $data['first_name'],
            'last_name'  => $data['last_name'],
            'account_id' => Account::first()->id,
        ];


        if ($user) {
            $user->update($userData);
        } else {
            $userData['email'] = $data['email'];
            $userData['password'] = AuthHelper::generateRandomPassword();
            $user = $user_repository->storeUser($userData);
            $user->assign(Role::EDITOR);
        }

        if ($user->propertyManager()->count() === 0) {
            return $this->storePropertyManager(['user_id' => $user->id, 'code' => $data['code']]);
        }
    }
}
