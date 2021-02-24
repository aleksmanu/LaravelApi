<?php
namespace App\Modules\Auth\Tests\Feature;

use App\Modules\Auth\Models\Role;
use App\Modules\Auth\Models\User;
use App\Modules\Common\Classes\EndpointTest;
use Illuminate\Support\Facades\Hash;

class AuthenticationTest extends EndpointTest
{
    public function testCanUsersLogin()
    {
        $tmpUsah = factory(User::class, 1)->create()->first();
        $tmpUsah->assign(Role::ADMIN);
        $tmpUsah->password = Hash::make('test123123');
        $tmpUsah->save();

        $result = $this->apiAs($this->dev_user, 'POST', '/api/auth/login/', [
            'email' => $tmpUsah->email,
            'password' => 'test123123',
        ], []);

        $tmpUsah->retract(Role::ADMIN);

        $result->assertSuccessful();
    }
}
