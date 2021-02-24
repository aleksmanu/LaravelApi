<?php

namespace App\Modules\Lease\Tests\Unit\LeaseController;

use App\Modules\Edits\Models\EditBatch;
use App\Modules\Lease\Http\Controllers\LeaseController;
use App\Modules\Lease\Models\Lease;
use App\Modules\TestFramework\Classes\BaseTestClass;

class GetEditAuditTrailTest extends BaseTestClass
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
     * @see LeaseController::getEditAuditTrail()
     * @test
     */
    public function test()
    {

        $edit_batch     = EditBatch::where('entity_type', Lease::class)->first();
        if ($edit_batch) {
            $lease = $edit_batch->entity;

            $week_ending_date = $edit_batch->created_at->endOfWeek()->toDateTimeString();

            $result = $this->apiAs($this->user, 'GET', '/api/lease/leases/edit-audit-trail/' . $lease->id, [
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
