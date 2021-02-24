<?php

namespace App\Modules\Client\Tests\Unit\Controllers\PortfolioController;

use App\Modules\Client\Http\Controllers\ClientAccountController;
use App\Modules\Client\Http\Controllers\PortfolioController;
use App\Modules\Client\Models\ClientAccount;
use App\Modules\Client\Models\Portfolio;
use App\Modules\Edits\Models\EditBatch;
use App\Modules\TestFramework\Classes\BaseTestClass;

class GetEditAuditTrailTest extends BaseTestClass
{

    /**
     * @var PortfolioController
     */
    private $controller;

    /**
     * @throws \Exception
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->controller = \App::make(PortfolioController::class);
    }

    /**
     * @see PortfolioController::getEditAuditTrail()
     * @test
     */
    public function test()
    {

        $edit_batch     = EditBatch::where('entity_type', Portfolio::class)->first();
        if ($edit_batch) {
            $portfolio = $edit_batch->entity;

            $week_ending_date = $edit_batch->created_at->endOfWeek()->toDateTimeString();

            $result = $this->apiAs($this->user, 'GET', '/api/client/portfolios/edit-audit-trail/' . $portfolio->id, [
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
