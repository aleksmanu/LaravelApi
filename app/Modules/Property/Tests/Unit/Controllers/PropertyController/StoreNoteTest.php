<?php

namespace App\Modules\Property\Tests\Unit\PropertyController;

use App\Modules\Property\Http\Controllers\PropertyController;
use App\Modules\Property\Models\Property;
use App\Modules\TestFramework\Classes\BaseTestClass;

class StoreNoteTest extends BaseTestClass
{

    /**
     * @var PropertyController
     */
    private $controller;

    /**
     * @throws \Exception
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->controller = \App::make(PropertyController::class);
    }

    /**
     * @throws \Exception
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * @see PropertyController::storeNote()
     * @test
     */
    public function test()
    {

        $property = Property::first();

        $result = $this->apiAs($this->user, 'POST', '/api/property/properties/' . $property->id . '/note', [
            'note' => $this->faker->paragraph
        ]);

        $result->assertSuccessful();
    }
}
