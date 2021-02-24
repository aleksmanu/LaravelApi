<?php

namespace App\Modules\Client\Tests\Unit\Controllers\PortfolioController;

use App\Modules\Client\Http\Controllers\PortfolioController;
use App\Modules\Client\Models\Portfolio;
use App\Modules\TestFramework\Classes\BaseTestClass;

class GetEditableTest extends BaseTestClass
{

    /**
     * @see PortfolioController::getEditable()
     * @test
     * @throws \Exception
     */
    public function test()
    {

        $result = $this->apiAs($this->user, 'GET', '/api/client/portfolios/get-editable', []);
        $result->assertSuccessful();

        $result = $result->json();

        $model = new Portfolio();
        foreach ($model->getEditable() as $k => $v) {
            $this->assertSame($v, $result[$k]);
        }
    }
}
