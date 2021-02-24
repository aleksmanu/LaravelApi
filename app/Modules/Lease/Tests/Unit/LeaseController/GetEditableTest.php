<?php

namespace App\Modules\Lease\Tests\Unit\LeaseController;

use App\Modules\Lease\Http\Controllers\LeaseController;
use App\Modules\Lease\Models\Lease;
use App\Modules\TestFramework\Classes\BaseTestClass;

class GetEditableTest extends BaseTestClass
{

    /**
     * @see LeaseController::getEditable()
     * @test
     * @throws \Exception
     */
    public function test()
    {

        $result = $this->apiAs($this->user, 'GET', '/api/lease/leases/get-editable', []);
        $result->assertSuccessful();

        $result = $result->json();

        $model = new Lease();
        foreach ($model->getEditable() as $k => $v) {
            $this->assertSame($v, $result[$k]);
        }
    }
}
