<?php

namespace App\Modules\Lease\Tests\Unit\TenantController;

use App\Modules\Lease\Http\Controllers\TenantController;
use App\Modules\Lease\Models\Tenant;
use App\Modules\TestFramework\Classes\BaseTestClass;

class GetEditableTest extends BaseTestClass
{

    /**
     * @see TenantController::getEditable()
     * @test
     * @throws \Exception
     */
    public function test()
    {

        $result = $this->apiAs($this->user, 'GET', '/api/lease/tenants/get-editable', []);
        $result->assertSuccessful();

        $result = $result->json();

        $model = new Tenant();
        foreach ($model->getEditable() as $k => $v) {
            $this->assertSame($v, $result[$k]);
        }
    }
}
