<?php

namespace App\Modules\Auth\Tests\Unit\Controllers\UserController;

use App\Modules\Account\Models\AccountType;
use App\Modules\Auth\Http\Controllers\UserController;
use App\Modules\Auth\Models\Role;
use App\Modules\Auth\Models\User;
use App\Modules\TestFramework\Classes\BaseTestClass;

class DatatableTest extends BaseTestClass
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
     * @see UserController::getUsersDatatable()
     * @test
     */
    public function test()
    {

        $request_data = $this->getRequestData();

        $result = $this->apiAs($this->user, 'GET', '/api/auth/users/data-table', $request_data);

        $result->assertSuccessful();

        $expected = \DB::table('users')
                       ->join('accounts', 'accounts.id', '=', 'users.account_id')
                       ->join('assigned_roles', function ($join) {
                           $join->on('users.id', '=', 'assigned_roles.entity_id')
                                ->where('assigned_roles.entity_type', User::class);
                       })->where('accounts.account_type_id', $request_data['account_type_id'])
                       ->where('accounts.id', $request_data['account_id'])
                       ->where('assigned_roles.role_id', $request_data['role_id'])
                       ->orderBy($request_data['sort_column'], $request_data['sort_order'])
                       ->skip($request_data['offset'])->take($request_data['limit'])
                       ->count();

        $this->assertSame($expected, count($result->json()['rows']));
    }

    /**
     * @return array
     */
    private function getRequestData()
    {

        $account_type = AccountType::first();

        return [
            'sort_column'     => 'first_name',
            'sort_order'      => 'desc',
            'limit'           => '25',
            'offset'          => '0',
            'account_type_id' => $account_type->id,
            'account_id'      => $account_type->accounts()->first()->id,
            'role_id'         => Role::first()->id
        ];
    }
}
