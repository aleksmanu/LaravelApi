<?php

namespace App\Modules\Lease\Tests\Unit\LeaseController;

use App\Modules\Lease\Http\Controllers\LeaseController;
use App\Modules\Lease\Models\Lease;
use App\Modules\TestFramework\Classes\BaseTestClass;

class StoreNoteTest extends BaseTestClass
{

    /**
     * @var LeaseController
     */
    private $controller;

    /**
     * @throws \Exception
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->controller = \App::make(LeaseController::class);
    }

    /**
     * @throws \Exception
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * @see LeaseController::storeNote()
     * @test
     */
    public function test()
    {

        $lease = Lease::first();

        $result = $this->apiAs($this->user, 'POST', '/api/lease/leases/' . $lease->id . '/note', [
            'note' => $this->faker->paragraph
        ]);

        $result->assertSuccessful();
    }
}
