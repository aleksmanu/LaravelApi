<?php

namespace App\Modules\Property\Tests\Unit\Controllers\PropertyManagerController;

use App\Modules\Auth\Models\Role;
use App\Modules\Property\Http\Controllers\PropertyManagerController;
use App\Modules\TestFramework\Classes\BaseTestClass;

class IndexTest extends BaseTestClass
{

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
     * @see PropertyManagerController::index()
     * @test
     */
    public function test()
    {

        $expected = \DB::table('property_managers')
                        ->whereNull('property_managers.deleted_at')
                       ->join('users', 'users.id', '=', 'property_managers.user_id')
                       ->orderBy('users.first_name', 'desc')->get();

        $result = $this->apiAs($this->user, 'GET', '/api/property/property-managers');
        $result->assertSuccessful();

        $this->assertSame(count($expected), count($result->json()));
    }
}
