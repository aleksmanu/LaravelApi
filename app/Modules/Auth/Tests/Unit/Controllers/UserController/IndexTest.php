<?php

namespace App\Modules\Auth\Tests\Unit\Controllers\UserController;

use App\Modules\Account\Models\AccountType;
use App\Modules\Auth\Http\Controllers\UserController;
use App\Modules\Auth\Models\Role;
use App\Modules\Auth\Models\User;
use App\Modules\TestFramework\Classes\BaseTestClass;

class IndexTest extends BaseTestClass
{

    /**
     * @return UserController|string
     */
    public function class()
    {
        return UserController::class;
    }

    /**
     * @setUp
     * @throws \Exception
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->user->assign(Role::DEVELOPER);
    }

    /**
     * @tearDown
     * @throws \Exception
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * @see UserController::index()
     * @test
     */
    public function test()
    {

        $request_data = $this->getRequestData();

        $expected = \DB::table('users')
                       ->join('accounts', 'accounts.id', '=', 'users.account_id')
                       ->join('assigned_roles', function ($join) {
                           $join->on('users.id', '=', 'assigned_roles.entity_id')
                                ->where('assigned_roles.entity_type', User::class);
                       })
                        ->whereNull('users.deleted_at')
                       ->orderBy('users.last_name', 'desc')->get();

        $result = $this->apiAs($this->user, 'GET', '/api/auth/users', $request_data);

        $result->assertSuccessful();
    }

    /**
     * @return array
     */
    private function getRequestData()
    {

        $account_type = AccountType::first();

        return [
            'account_type_id' => $account_type->id,
            'account_id'      => $account_type->accounts()->first()->id,
            'role_id'         => Role::first()->id
        ];
    }
}
