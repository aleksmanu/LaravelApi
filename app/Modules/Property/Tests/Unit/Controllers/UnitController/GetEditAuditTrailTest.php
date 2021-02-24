<?php

namespace App\Modules\Property\Tests\Unit\UnitController;

use App\Modules\Edits\Models\EditBatch;
use App\Modules\Property\Http\Controllers\UnitController;
use App\Modules\Property\Models\Unit;
use App\Modules\TestFramework\Classes\BaseTestClass;

class GetEditAuditTrailTest extends BaseTestClass
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
     * @see UnitController::getEditAuditTrail()
     * @test
     */
    public function test()
    {

        $edit_batch = EditBatch::where('entity_type', Unit::class)->first();
        if ($edit_batch) {
            $unit = $edit_batch->entity;

            $week_ending_date = $edit_batch->created_at->endOfWeek()->toDateTimeString();

            $result = $this->apiAs($this->user, 'GET', '/api/property/units/edit-audit-trail/' . $unit->id, [
                'week_ending_date' => $week_ending_date
            ]);

            $result->assertSuccessful();
            $result = $result->json();

            foreach ($result as $datum) {
                $this->assertArrayHasKey('edits', $datum);
                $this->assertArrayHasKey('created_by_user', $datum);
                $this->assertArrayHasKey('reviewed_by_user', $datum);
                $this->assertArrayHasKey('edit_batch_type', $datum);

                foreach ($datum['edits'] as $edit) {
                    $this->assertArrayHasKey('edit_status', $edit);
                }
            }
        } else {
            $this->markTestSkipped('No edit data to test against');
        }
    }
}
