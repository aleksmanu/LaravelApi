<?php
namespace App\Modules\Auth\Repositories\Eloquent;

use App\Modules\Account\Models\Account;
use App\Modules\Auth\Models\Role;
use \App\Modules\Auth\Models\User;
use App\Modules\Common\Traits\HasServerSideSortingTrait;
use Carbon\Carbon;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Facades\Excel;

class UserRepository
{
    use HasServerSideSortingTrait;

    /**
     * @var User
     */
    protected $model;

    /**
     * UserRepository constructor.
     * @param User $model
     */
    public function __construct(User $model)
    {
        $this->model = $model;
    }

    /**
     * @param $sort_attr
     * @param $sort_dir
     * @param $page_limit
     * @param $offset
     * @param $account_type_id
     * @param $account_id
     * @param $role_id
     * @param $user_name_partial
     * @param $archived include archived users
     * @param $unarchived include none archived users
     * @return Collection
     */
    public function getUsersDataTable(
        $sort_attr,
        $sort_dir,
        $page_limit,
        $offset,
        $account_type_id,
        $account_id,
        $role_id,
        $user_name_partial,
        bool $archived,
        bool $unarchived
    ): Collection {

        $query = $this->applyFilters(
            $this->getBaseQuery($archived, $unarchived),
            $account_type_id,
            $account_id,
            $role_id,
            $user_name_partial
        );

        // Get a copy of the query at this point with no limits applied, otherwise total count will be skewed
        // Insert the monster query inside a simple count() query. Replace bindings first
        $countSelect = 'SELECT count(*) as row_count FROM (' . $query->toSql() . ') AS T1';
        $bindParams = $query->getBindings(); //keep a hold of these before sort and limit is added

        $this->johnifySortColumn($sort_attr);

        $query->skip($offset)
              ->take($page_limit)
              ->orderBy($sort_attr, $sort_dir);

        return collect([
            'row_count' => \DB::select($countSelect, $bindParams)[0]->row_count,
            'rows' => $query->get()
        ]);
    }


    /**
     * @param $account_type_id
     * @param $account_id
     * @param $role_id
     * @param $user_name_partial
     * @return Collection
     */
    public function getUsers($account_type_id = 0, $account_id = 0, $role_id = 0, $user_name_partial = ''): Collection
    {
        $query = $this->applyFilters(
            $this->getBaseQuery(),
            $account_type_id,
            $account_id,
            $role_id,
            $user_name_partial
        );
        $query->orderBy('users.last_name', 'asc');

        return $query->get();
    }


    /**
     * @param $role_id
     * @return Collection
     */
    public function getUsersByRole($role_id, $account_id): Collection
    {
        $role = Role::findOrFail($role_id);
        
        $query = User::whereIs($role->name)
            ->join('accounts', 'accounts.id', '=', 'users.account_id');

        if ($account_id != 0) {
            $query->where('accounts.id', '=', $account_id);
        }
        return $query->get();
    }

    /**
     * @param $id
     * @return User
     */
    public function getUser($id): User
    {
        return $this->getBaseQuery(true, true)->findOrFail($id);
    }

    /**
     * @param array $data
     * @return User
     */
    public function storeUser(array $data): User
    {
        return $this->model->create($data);
    }

    /**
     * @param $user
     * @param array $data
     * @return User
     */
    public function updateUser($user, array $data): User
    {

        if (!$user instanceof User) {
            $user = $this->getUser($user);
        }

        $user->update($data);
        return $user;
    }

    /**
     * @param $id
     * @return User
     * @throws \Exception
     */
    public function deleteUser($id): User
    {
        $user = $this->getUser($id);
        $role = $user->roles[0];
        $user->delete();
        $user->assign($role);
        return $user;
    }

    /**
     * @param $id
     * @return User
     * @throws \Exception
     */
    public function restore($id): User
    {
        $user = $this->getUser($id);
        $user->restore();
        return $user;
    }

    /**
     * @return Builder
     */
    private function getBaseQuery($archived = false, $unarchived = true)
    {
        if ($archived && $unarchived) {
            $query = $this->model::withTrashed();
        } elseif ($archived && !$unarchived) {
            $query = $this->model::onlyTrashed();
        } elseif (!$archived && $unarchived) {
            $query = $this->model;
        } elseif (!$archived && !$unarchived) {
            $query = $this->model->where('users.id', '=', 0);
        }
        return $query->join('accounts', 'accounts.id', '=', 'users.account_id')
            ->join('assigned_roles', function ($join) {
                $join->on('users.id', '=', 'assigned_roles.entity_id')
                    ->where('assigned_roles.entity_type', User::class);
            })
            ->select('users.*')
            ->leftJoin('account_types', 'account_types.id', 'accounts.account_type_id');
    }

    /**
     * @param $query
     * @param int $account_type_id
     * @param int $account_id
     * @param int $role_id
     * @param string $user_name_partial
     * @return mixed
     */
    private function applyFilters($query, $account_type_id = 0, $account_id = 0, $role_id = 0, $user_name_partial = '')
    {

        if ($account_type_id) {
            $query->where('accounts.account_type_id', $account_type_id);
        }

        if ($account_id) {
            $query->where('accounts.id', $account_id);
        }

        if ($role_id) {
            $query->where('assigned_roles.role_id', $role_id);
        }

        if ($user_name_partial) {
            $query->where(function ($query) use ($user_name_partial) {
                $query->where($this->model->getTableName() . '.first_name', 'LIKE', '%' . $user_name_partial . '%')
                      ->orWhere($this->model->getTableName() . '.last_name', 'LIKE', '%' . $user_name_partial . '%');
            });
        }

        return $query;
    }


    public function sheetExport()
    {
        $userObjs = User::all();
        $users = $userObjs->toArray();
        $time = Carbon::now();

        foreach ($users as $key => $user) {
            $user['role'] = $user['role']['name'];
            $user['account_name'] = $user['account']['name'];
            $user['password'] = $userObjs[$key]->password;
            unset($user['account']);
            $users[$key] = $user;
        }

        Excel::create('Cluttons User Export', function ($excel) use ($time, $users) {

            // Set the title
            $excel->setTitle('Cluttons User Export ' . $time);

            // Chain the setters
            $excel->setCreator('Cluttons Portal')
                ->setCompany('Cluttons')
                ->setDescription('- reserved -');

            $excel->sheet('Users', function ($sheet) use ($users) {
                $sheet->fromArray($users);
            });
        })->export('csv');
    }

    public function sheetImport($data, $shouldNukeFirst = false)
    {
        $users = $data->all();

        \DB::transaction(function () use ($users, $shouldNukeFirst) {
            Schema::disableForeignKeyConstraints();
            if ($shouldNukeFirst) {
                foreach (User::all() as $user) {
                    $user->retract($user->role);
                }

                User::truncate();
            }

            foreach ($users as $user) {
                $account = Account::findOrFail($user['account_id']);
                $name    = $account->name;
                $uID     = $user['account_id'];
                $uName   = $user['account_name'];

                if ($account->name !== $user['account_name']) {
                    throw new \Exception(
                        "User account_id is desynchronized [$uID-$uName] !== $name]",
                        500
                    );
                }

                $newUser = new User($user->toArray());
                $newUser->id = $user->id;
                $newUser->password = $user->password;
                $newUser->save();

                $newUser->assign($user['role']);
            }

            Schema::enableForeignKeyConstraints();
        });
    }
}
