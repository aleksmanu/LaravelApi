<?php

namespace App\Modules\Dashboard\Tests\Unit\EditDashboardController;

use App\Modules\Dashboard\Http\Controllers\EditDashboardController;
use App\Modules\TestFramework\Classes\BaseTestClass;

class GetEditApprovalSplitTest extends BaseTestClass
{

    /**
     * @see EditDashboardController::getEditApprovalSplit()
     * @test
     */
    public function test()
    {

        $result = $this->apiAs($this->user, 'GET', '/api/dashboard/edit/approval-split', []);
        $result->assertSuccessful();

        $result = $result->json();

        foreach ($result as $datum) {
            $this->assertArrayHasKey('name', $datum);
            $this->assertArrayHasKey('value', $datum);
        }
    }
}
