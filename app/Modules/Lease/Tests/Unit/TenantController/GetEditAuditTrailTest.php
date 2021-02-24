<?php

namespace App\Modules\Lease\Tests\Unit\TenantController;

use App\Modules\Edits\Models\EditBatch;
use App\Modules\Lease\Http\Controllers\TenantController;
use App\Modules\Lease\Models\Tenant;
use App\Modules\TestFramework\Classes\BaseTestClass;

class GetEditAuditTrailTest extends BaseTestClass
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
     * @see TenantController::getEditAuditTrail()
     * @test
     */
    public function test()
    {

        $edit_batch     = EditBatch::where('entity_type', Tenant::class)->first();
        if ($edit_batch) {
            $tenant = $edit_batch->entity;

            $week_ending_date = $edit_batch->created_at->endOfWeek()->toDateTimeString();

            $result = $this->apiAs($this->user, 'GET', '/api/lease/tenants/edit-audit-trail/' . $tenant->id, [
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
