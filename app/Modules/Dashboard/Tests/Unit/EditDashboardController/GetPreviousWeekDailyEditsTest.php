<?php

namespace App\Modules\Dashboard\Tests\Unit\EditDashboardController;

use App\Modules\Dashboard\Http\Controllers\EditDashboardController;
use App\Modules\TestFramework\Classes\BaseTestClass;

class GetPreviousWeekDailyEditsTest extends BaseTestClass
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
     * @see EditDashboardController::getPreviousWeekDailyEdits()
     * @test
     */
    public function test()
    {

        $result = $this->apiAs($this->user, 'GET', '/api/dashboard/edit/previous-week-edits', []);

        $result->assertSuccessful();
        $result = $result->json();

        foreach ($result as $datum) {
            $this->assertArrayHasKey('name', $datum);
            $this->assertArrayHasKey('series', $datum);
            $this->assertSame(count($datum['series']), 7);

            foreach ($datum['series'] as $row) {
                $this->assertArrayHasKey('value', $row);
                $this->assertArrayHasKey('name', $row);
            }
        }
    }
}
