<?php

namespace App\Modules\Property\Tests\Unit\UnitController;

use App\Modules\Property\Http\Controllers\UnitController;
use App\Modules\Property\Models\Unit;
use App\Modules\TestFramework\Classes\BaseTestClass;

class StoreNoteTest extends BaseTestClass
{

    /**
     * @var UnitController
     */
    private $controller;

    /**
     * @throws \Exception
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->controller = \App::make(UnitController::class);
    }

    /**
     * @throws \Exception
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * @see UnitController::storeNote()
     * @test
     */
    public function test()
    {

        $unit = Unit::first();

        $result = $this->apiAs($this->user, 'POST', '/api/property/units/' . $unit->id . '/note', [
            'note' => $this->faker->paragraph
        ]);

        $result->assertSuccessful();
    }
}
