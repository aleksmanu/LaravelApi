<?php
namespace App\Modules\Auth\Database\Seeds;

use App\Modules\Account\Models\Account;
use App\Modules\Account\Models\AccountType;
use App\Modules\Auth\Models\Role;
use App\Modules\Auth\Repositories\Eloquent\RoleRepository;
use App\Modules\Property\Models\PropertyManager;
use App\Modules\Auth\Models\User;
use App\Modules\TestFramework\Classes\SeedClasses\UserSeed;
use Faker\Factory;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * @var int
     */
    private $count = 5;

    /**
     * @var Factory
     */
    private $faker;

    protected $roleRepo;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->faker = Factory::create('en_GB');

        for ($i = 0; $i < $this->count; $i++) {
            $this->createAccount();
            $this->createPropertyManager();
        }

        $this->createExternalUsers();
    }

    /**
     * @return mixed
     */
    private function createAccount()
    {
        $account = factory(Account::class)->create();

        for ($i = 0; $i < $this->count; $i++) {
            $user = User::create(UserSeed::generate($account));
            $user->assign(Role::CLIENT);
        }
        return $account;
    }

    /**
     * TODO - Change this
     * @return mixed
     */
    private function createPropertyManager()
    {

        $user = User::create(UserSeed::generate(Account::first()));
        $user->assign(Role::EDITOR); // TODO - This is temporary

        $property_manager_data = [
            'user_id' => $user->id,
            'code'    => $this->faker->bothify('?#?#?#')
        ];
        return PropertyManager::create($property_manager_data);
    }

    private function createExternalUsers()
    {
        $extAccount = Account::select(Account::getTableName().'.*')
            ->leftJoin(
                AccountType::getTableName(),
                Account::getTableName() . '.account_type_id',
                '=',
                AccountType::getTableName() . '.id'
            )->where(AccountType::getTableName().'.slug', AccountType::EXTERNAL)
            ->first();

        //dd($extAccount);

        $this->roleRepo = app()->make(RoleRepository::class);
        foreach ($this->roleRepo->getExternalRoles() as $externalRole) {
            for ($i = 0; $i < $this->count; $i++) {
                $user = User::create(UserSeed::generate($extAccount));
                $user->assign($externalRole);
            }
        }
    }
}
