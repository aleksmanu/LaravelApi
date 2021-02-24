<?php

namespace App\Modules\Dashboard\Tests\Unit\EditDashboardController;

use App\Modules\Dashboard\Http\Controllers\EditDashboardController;
use App\Modules\TestFramework\Classes\BaseTestClass;

class GetTenantReviewStatsTest extends BaseTestClass
{

    /**
     * @var EditDashboardController
     */
    private $controller;

    /**
     * @throws \Exception
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->controller = \App::make(EditDashboardController::class);
    }

    /**
     * @see EditDashboardController::getPortfolioReviewStats()
     */
    public function test()
    {

        $result = $this->apiAs($this->user, 'GET', '/api/dashboard/edit/portfolio-review-stats', []);

        $result->assertSuccessful();
        $result = $result->json();

        foreach ($result as $datum) {
            $this->assertArrayHasKey('value', $datum);
            $this->assertArrayHasKey('name', $datum);
        }
    }
}
