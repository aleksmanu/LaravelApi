<?php

namespace App\Modules\Property\Tests\Unit\Controllers\UnitController;

use App\Modules\Property\Http\Controllers\UnitController;
use App\Modules\Property\Models\Unit;
use App\Modules\TestFramework\Classes\BaseTestClass;

class GetEditableTest extends BaseTestClass
{

    /**
     * @see UnitController::getEditable()
     * @test
     * @throws \Exception
     */
    public function test()
    {

        $result = $this->apiAs($this->user, 'GET', '/api/property/units/get-editable', []);
        $result->assertSuccessful();

        $result = $result->json();

        $model = new Unit();
        foreach ($model->getEditable() as $k => $v) {
            $this->assertSame($v, $result[$k]);
        }
    }
}
