<?php

namespace App\Modules\Auth\Tests\Feature;

use App\Modules\Account\Models\Account;
use App\Modules\Account\Models\AccountType;
use App\Modules\Auth\Http\Controllers\UserController;
use App\Modules\Auth\Models\User;
use App\Modules\Common\Classes\EndpointTest;

class UserEndpointTest extends EndpointTest
{

    private $account_type_id;
    private $account_id;
    private $role_id;
    private $sort_attr = 'last_name';
    private $sort_dir  = 'dir';
    private $limit     = 20;
    private $offset    = 0;

    public function setUp(): void
    {
        parent::setUp();

        $this->account_type_id = AccountType::randomRow()->id;
        $this->account_id      = Account::randomRow()->id;
        $this->role_id         = \Bouncer::role()->first()->id;
    }


    /**
     * @return mixed
     */
    private function getExpectedUsers()
    {

        $query = \DB::table('users')->join('accounts', 'accounts.id', '=', 'users.account_id')
                    ->join('assigned_roles', function ($join) {
                        $join->on('users.id', '=', 'assigned_roles.entity_id')
                             ->where('assigned_roles.entity_type', User::class);
                    })
                    ->select('users.*')
                    ->orderBy('users.last_name', 'asc')
                    ->where('account_id', $this->account_id)
                    ->where('account_type_id', $this->account_type_id)
                    ->where('role_id', $this->role_id)
                    ->orderBy($this->sort_attr, $this->sort_dir)
                    ->take($this->limit)
                    ->skip($this->offset);
        return $query->get();
    }

    /**
     * @see UserController::index();
     */
    public function testCanUsersIndexUsers()
    {
        $this->assertGetCountForAllUserTypes('/api/auth/users/', User::query());
    }

    /**
     * @see UserController::index();
     */
    public function testCanUsersFilterUsers()
    {
        $result = $this->apiAs($this->dev_user, 'GET', '/api/auth/users/', [
            'account_type_id' => $this->account_type_id,
        ], []);
        $result->assertSuccessful();
        foreach ($result->json() as $item) {
            $this->assertEquals($item['account']['account_type_id'], $this->account_type_id);
        }

        $result = $this->apiAs($this->dev_user, 'GET', '/api/auth/users/', [
            'account_id'      => $this->account_id,
        ], []);
        $result->assertSuccessful();
        foreach ($result->json() as $item) {
            $this->assertEquals($item['account_id'], $this->account_id);
        }

        $result = $this->apiAs($this->dev_user, 'GET', '/api/auth/users/', [
            'role_id'         => $this->role_id,
        ], []);
        $result->assertSuccessful();
        foreach ($result->json() as $item) {
            $this->assertEquals($item['role']['id'], $this->role_id);
        }

        $result = $this->apiAs($this->dev_user, 'GET', '/api/auth/users/', [
            'user_name_partial'         => 'a',
        ], []);
        $result->assertSuccessful();
        foreach ($result->json() as $item) {
            $this->assertContains('a', strtolower($item['first_name'] . $item['last_name']));
        }
        $result = $this->apiAs($this->dev_user, 'GET', '/api/auth/users/', [
            'user_name_partial'         => 'b',
        ], []);
        $result->assertSuccessful();
        foreach ($result->json() as $item) {
            $this->assertContains('b', strtolower($item['first_name'] . $item['last_name']));
        }
        $result = $this->apiAs($this->dev_user, 'GET', '/api/auth/users/', [
            'user_name_partial'         => 'c',
        ], []);
        $result->assertSuccessful();
        foreach ($result->json() as $item) {
            $this->assertContains('c', strtolower($item['first_name'] . $item['last_name']));
        }
    }

    public function testCanUsersSortUsersDataTable()
    {
        // Try with two values to avoid the .00001% chance the size of the full table is the attempted page size
        $result = $this->apiAs($this->dev_user, 'GET', '/api/auth/users/data-table', [
            'sort_column' => 'id',
            'sort_order'  => 'desc'
        ], []);
        $result->assertSuccessful();
        $this->assertSame(
            User::orderBy(User::getTableName() . '.id', 'desc')->get(),
            $result->json()['rows']
        );

        $result = $this->apiAs($this->dev_user, 'GET', '/api/auth/users/data-table', [
            'sort_column' => 'id',
            'sort_order'  => 'asc'
        ], []);
        $result->assertSuccessful();
        $this->assertSame(
            User::orderBy(User::getTableName() . '.id', 'asc')->get(),
            $result->json()['rows']
        );
    }

    /**
     * @see UserController::datatable()
     */
    public function testCanUsersPaginateUsers()
    {

        $result = $this->apiAs($this->dev_user, 'GET', '/api/auth/users/data-table', [
            'account_type_id' => $this->account_type_id,
            'account_id'      => $this->account_id,
            'role_id'         => $this->role_id,
            'offset'          => $this->offset,
            'limit'           => $this->limit,
            'sort_column'     => $this->sort_attr,
            'sort_order'      => $this->sort_dir
        ], []);

        $result->assertSuccessful();
        $expected = $this->getExpectedUsers();
        $this->assertSame(count($expected), count($result->json()['rows']));
    }

    public function testCanUsersReadUsers()
    {
        $existing_id = User::first()->id;
        $this->assertGetForAllUserTypes('/api/auth/users/' . $existing_id);
        $this->apiAs($this->dev_user, 'GET', '/api/auth/users/-1', [], [])->assertStatus(404);
    }

    public function testCanUsersCreateUsers()
    {
        // TODO update with user scope / account_id and stuff
        $this->apiAs($this->dev_user, 'POST', '/api/auth/users/', [
            'account_id' => $this->account_id,
            'first_name' => 'Dummy',
            'last_name'  => 'McDummyface',
            'email'      => 'testmail@nulldomain.com',
            'password'   => 'hesoyam',
            'role_id'    => \Bouncer::role()->first()->id,
        ], [])->assertSuccessful();

        $this->assertInstanceOf(User::class, User::where('last_name', 'McDummyface')->first());
    }

    public function testDoesUserControllerValidateProperly()
    {
        $this->apiAs($this->dev_user, 'POST', '/api/auth/users/', [
            'account_id' => -1,
            'first_name' => '',
            'last_name'  => '',
            'email'      => 'NotAValidEmailAddress',
            'password'   => '',
            'role'       => 'ThisRoleWillNeverExist!',
        ], [])->assertJsonValidationErrors([
            'account_id', 'first_name', 'last_name', 'email', 'password', 'role_id'
        ]);
    }

    public function testCanUsersUpdateUsers()
    {
        // TODO update with user scope / account_id and stuff
        //Store the original for subsequent comparison
        $original_dev_user = clone $this->dev_user;

        $this->apiAs($this->dev_user, 'PATCH', '/api/auth/users/' . $this->dev_user->id, [
            'account_id' => Account::where('id', '!=', $this->dev_user->account_id)->first()->id,
            'id'         => $this->dev_user->id,
            'first_name' => $this->dev_user->first_name . "CHANGED",
            'last_name'  => $this->dev_user->last_name . "CHANGED",
            'email'      => "NoWayThisIsNotUnique" . $this->dev_user->email,
            'role_id'    => \Bouncer::role()->first()->id,
        ], [])->assertSuccessful();

        //Reload the model to catch database changes
        $this->dev_user = $this->dev_user->fresh();

        //Check differences are as expected
        $this->assertNotEquals($this->dev_user->account_id, $original_dev_user->account_id);
        $this->assertEquals($this->dev_user->first_name, $original_dev_user->first_name . "CHANGED");
        $this->assertEquals($this->dev_user->last_name, $original_dev_user->last_name . "CHANGED");
        $this->assertEquals($this->dev_user->email, "NoWayThisIsNotUnique" . $original_dev_user->email);
        $this->assertEquals($this->dev_user->role->id, \Bouncer::role()->first()->id);
    }

//    public function testCanUsersDeleteUsers()
//    {
//        $unlucky_record = (factory(User::class, 1)->create())->first();
//        $unlucky_record->save();
//        $this->assertInstanceOf(User::class, $unlucky_record);
//        $this->apiAs($this->dev_user, 'DELETE', '/api/auth/users/' . $unlucky_record->id, [], [])->assertSuccessful();
//        $this->assertNull(User::find($unlucky_record->id));
//    }
}
