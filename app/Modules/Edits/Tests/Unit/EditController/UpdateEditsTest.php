<?php

namespace App\Modules\Edits\Tests\Unit\EditController;

use App\Modules\Core\Library\EloquentHelper;
use App\Modules\Edits\Http\Controllers\EditController;
use App\Modules\Edits\Models\Edit;
use App\Modules\Edits\Models\EditBatch;
use App\Modules\Edits\Models\EditStatus;
use App\Modules\TestFramework\Classes\BaseTestClass;

class UpdateEditsTest extends BaseTestClass
{

    /**
     * @var EditController
     */
    private $controller;

    /**
     * @var EditBatch
     */
    private $edit_batch;

    /**
     * @throws \Exception
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->controller = \App::make(EditController::class);
        $this->edit_batch = EditBatch::first();
    }

    /**
     * @throws \Exception
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * @see EditController::updateEdits()
     * @test
     */
    public function test()
    {
        if ($this->edit_batch) {
            $request_data = $this->getRequestData();

            $result = $this->apiAs($this->user, 'POST', '/api/edits/edits/update-edits', $request_data);

            $result->assertSuccessful();
        } else {
            $this->markTestSkipped('No edit data to test against');
        }
    }

    /**
     * @return mixed
     */
    private function getRequestData()
    {

        $data['edits']    = array_keys($this->edit_batch->edits->keyBy('id')->toArray()); //Test for complete submission
        $data['approved'] = true;
        $data['note']     = $this->faker->paragraph;
        return $data;
    }
}
