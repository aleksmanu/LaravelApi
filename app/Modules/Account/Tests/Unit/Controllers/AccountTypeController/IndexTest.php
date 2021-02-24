<?php

namespace App\Modules\Account\Tests\Unit\Controllers\AccountTypeController;

use App\Modules\Account\Http\Controllers\AccountTypeController;
use App\Modules\Auth\Models\Role;
use App\Modules\TestFramework\Classes\BaseTestClass;

class IndexTest extends BaseTestClass
{

    /**
     * @return AccountTypeController|string
     */
    public function class()
    {
        return AccountTypeController::class;
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
     * @see AccountTypeController::index()
     * @test
     */
    public function test()
    {

        $expected = \DB::table('account_types')->whereNull('deleted_at')->orderBy('name', 'asc')->get();

        $result = $this->apiAs($this->user, 'GET', '/api/account/account-types');

        $result->assertSuccessful();

        $this->assertSame(count($expected), count($result->json()));
    }
}
