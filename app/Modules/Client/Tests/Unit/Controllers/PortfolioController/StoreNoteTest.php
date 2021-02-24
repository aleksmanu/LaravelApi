<?php

namespace App\Modules\Client\Tests\Unit\Controllers\PortfolioController;

use App\Modules\Client\Http\Controllers\PortfolioController;
use App\Modules\Client\Models\Portfolio;
use App\Modules\TestFramework\Classes\BaseTestClass;

class StoreNoteTest extends BaseTestClass
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
     * @throws \Exception
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * @see PortfolioController::storeNote()
     * @test
     */
    public function test()
    {

        $portfolio = Portfolio::first();

        $result = $this->apiAs($this->user, 'POST', '/api/client/portfolios/' . $portfolio->id .'/note', [
            'note' => $this->faker->paragraph
        ]);

        $result->assertSuccessful();
    }
}
