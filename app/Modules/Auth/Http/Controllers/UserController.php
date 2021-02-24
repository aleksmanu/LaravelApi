<?php
namespace App\Modules\Auth\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\Account\Models\Account;
use App\Modules\Account\Models\AccountType;
use App\Modules\Auth\Http\Requests\Users\UserDatatable;
use App\Modules\Auth\Http\Requests\Users\UserIndex;
use App\Modules\Auth\Http\Requests\Users\UserStoreRequest;
use App\Modules\Auth\Http\Requests\Users\UserUpdateRequest;
use App\Modules\Auth\Repositories\Eloquent\RoleRepository;
use App\Modules\Auth\Repositories\Eloquent\UserRepository;
use App\Modules\Auth\Http\Requests\Users\UserRoleRequest;

class UserController extends Controller
{
    /**
     * @var UserRepository
     */
    protected $user_repository;

    /**
     * @var RoleRepository
     */
    protected $role_repository;

    /**
     * UserController constructor.
     * @param UserRepository $user_repository
     * @param RoleRepository $role_repository
     */
    public function __construct(UserRepository $user_repository, RoleRepository $role_repository)
    {
        $this->user_repository = $user_repository;
        $this->role_repository = $role_repository;
    }

    /**
     * @param UserIndex $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function index(UserIndex $request)
    {
        return response($this->user_repository->getUsers(
            intval($request->account_type_id),
            intval($request->account_id),
            intval($request->role_id),
            $request->user_name_partial
        ));
    }


    public function onlyClientUsersFiltered(Request $request)
    {
        $sysUsers = $this->user_repository->getUsers(0, Account::getByType(AccountType::SYSTEM)->id, 0, '');
        $extUsers = $this->user_repository->getUsers(0, Account::getByType(AccountType::EXTERNAL)->id, 0, '');
        $clientUsers = $this->user_repository->getUsers(0, $request['account_id'], 0, '');

        return response($sysUsers->concat($extUsers)->concat($clientUsers));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function getUsersByRole(UserRoleRequest $request)
    {
        return response(
            $this->user_repository->getUsersByRole(
                intval($request->role_id),
                auth()->payload()->get('restriction_account_id')
            )
        );
    }

    /**
     * @param UserDatatable $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function datatable(UserDatatable $request)
    {
        return response($this->user_repository->getUsersDataTable(
            $request->sort_column ?? config('misc.dataTable.defaultSortColumn'),
            $request->sort_order ?? config('misc.dataTable.defaultSortOrder'),
            $request->limit ?? config('misc.dataTable.defaultPerPage'),
            intval($request->offset),
            intval($request->account_type_id),
            intval($request->account_id),
            intval($request->role_id),
            $request->user_name_partial,
            (bool) $request->archived,
            (bool) $request->unarchived
        ));
    }

    /**
     * @param UserStoreRequest $request
     * @return \Illuminate\Http\Response
     * @throws \Exception
     * @throws \Throwable
     */
    public function store(UserStoreRequest $request)
    {
        $validated_data = $request->validated();

        switch ($validated_data['user_scope']) {
            case 'scope-sys':
                $validated_data['account_id'] = Account::getByType('system')->id;
                break;
            case 'scope-ext':
                $validated_data['account_id'] = Account::getByType('external')->id;
                break;
        }

        $user = \DB::transaction(function () use ($validated_data) {
            $validated_data['password'] = \Hash::make($validated_data['password']);
            $user = $this->user_repository->storeUser($validated_data);
            $role = $this->role_repository->getRole($validated_data['role_id']);
            $user->assign($role->name);

            return $user;
        });

        return response($user);
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function show($id)
    {
        return response($this->user_repository->getUser($id));
    }

    /**
     * @param UserUpdateRequest $request
     * @param int $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     * @throws \Throwable
     */
    public function update(UserUpdateRequest $request, int $id)
    {
        $validated_data = $request->validated();

        switch ($validated_data['user_scope']) {
            case 'scope-sys':
                $validated_data['account_id'] = Account::getByType('system')->id;
                break;
            case 'scope-ext':
                $validated_data['account_id'] = Account::getByType('external')->id;
                break;
        }

        $user = \DB::transaction(function () use ($validated_data, $id) {

            if (!empty($validated_data['password'])) {
                $validated_data['password'] = \Hash::make($validated_data['password']);
            } else {
                unset($validated_data['password']);
            }

            $role = $this->role_repository->getRole($validated_data['role_id']);
            $user = $this->user_repository->getUser($id);

            if ($user->role->id !== $role->id) {
                $user->retract($user->role->name);
                $user->assign($role->name);
            }

            return $this->user_repository->updateUser($user, $validated_data);
        });
        return response($user);
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return response($this->user_repository->deleteUser($id));
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function restore($id)
    {
        return response($this->user_repository->restore($id));
    }
}
