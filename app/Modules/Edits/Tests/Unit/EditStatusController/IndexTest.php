<?php
namespace App\Modules\Edits\Tests\Unit\EditStatusController;

use App\Modules\Edits\Http\Controllers\EditStatusController;
use App\Modules\Edits\Models\EditStatus;
use App\Modules\TestFramework\Classes\BaseTestClass;
use App\Modules\TestFramework\Helpers\InstanceHelper;

class IndexTest extends BaseTestClass
{

    /**
     * @var EditStatusController
     */
    private $controller;

    /**
     * @throws \Exception
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->controller = \App::make(EditStatusController::class);
    }

    /**
     * @throws \Exception
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * @see EditStatusController::index()
     * @test
     */
    public function test()
    {

        $expected = EditStatus::all();

        $result = $this->apiAs($this->user, 'GET', '/api/edits/edit-statuses', []);

        $result = $result->json();

        foreach ($result as $k => $v) {
            InstanceHelper::assertInstance(EditStatus::class, $expected[$k], $v);
        }
    }
}
