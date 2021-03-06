<?php

namespace App\Modules\Dashboard\Tests\Unit\EditDashboardController;

use App\Modules\Dashboard\Http\Controllers\EditDashboardController;
use App\Modules\TestFramework\Classes\BaseTestClass;

class GetReviewedEditsTotalTest extends BaseTestClass
{

    /**
     * @see EditDashboardController::getReviewedEditsTotal()
     * @test
     */
    public function test()
    {

        $result = $this->apiAs($this->user, 'GET', '/api/dashboard/edit/reviewed-edits-total', []);
        $result->assertSuccessful();
        $result = $result->json();

        foreach ($result as $datum) {
            $this->assertArrayHasKey('name', $datum);
            $this->assertArrayHasKey('value', $datum);
        }
    }
}
