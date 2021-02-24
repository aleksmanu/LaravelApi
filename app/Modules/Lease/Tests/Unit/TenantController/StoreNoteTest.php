<?php

namespace App\Modules\Lease\Tests\Unit\TenantController;

use App\Modules\Lease\Http\Controllers\TenantController;
use App\Modules\Lease\Models\Tenant;
use App\Modules\TestFramework\Classes\BaseTestClass;

class StoreNoteTest extends BaseTestClass
{

    /**
     * @var TenantController
     */
    private $controller;

    /**
     * @throws \Exception
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->controller = \App::make(TenantController::class);
    }

    /**
     * @see TenantController::storeNote()
     * @test
     */
    public function test()
    {

        $tenant = Tenant::first();

        $result = $this->apiAs($this->user, 'POST', '/api/lease/tenants/' . $tenant->id . '/note', [
            'note' => $this->faker->paragraph
        ]);

        $result->assertSuccessful();
    }
}
