<?php

namespace App\Modules\Auth\Tests\Unit\Controllers\RoleController;

use App\Modules\Auth\Http\Controllers\RoleController;
use App\Modules\Auth\Models\Role;
use App\Modules\TestFramework\Classes\BaseTestClass;

class IndexTest extends BaseTestClass
{

    /**
     * @return RoleController|string
     */
    public function class()
    {
        return RoleController::class;
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
     * @see RoleController::index()
     * @test
     */
    public function test()
    {

        $expected = \DB::table('roles')->orderBy('name', 'desc')->get();

        $result = $this->apiAs($this->user, 'GET', '/api/auth/roles');
        $result->assertSuccessful();

        $this->assertSame(count($expected), count($result->json()));
    }
}
