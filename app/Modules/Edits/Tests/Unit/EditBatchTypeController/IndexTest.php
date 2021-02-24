<?php

namespace App\Modules\Edits\Tests\Unit\EditBatchTypeController;

use App\Modules\Edits\Http\Controllers\EditBatchTypeController;
use App\Modules\Edits\Models\EditBatchType;
use App\Modules\TestFramework\Classes\BaseTestClass;
use App\Modules\TestFramework\Helpers\InstanceHelper;

class IndexTest extends BaseTestClass
{

    /**
     * @var EditBatchTypeController
     */
    private $controller;

    /**
     * @throws \Exception
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->controller = \App::make(EditBatchTypeController::class);
    }

    /**
     * @throws \Exception
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * @see EditBatchTypeController::index()
     * @test
     */
    public function test()
    {
        $expected = EditBatchType::orderBy('name', 'asc')->get();

        $result = $this->apiAs($this->user, 'GET', '/api/edits/edit-batch-types', []);

        $result = $result->json();

        foreach ($result as $k => $v) {
            InstanceHelper::assertInstance(EditBatchType::class, $expected[$k], $v);
        }
    }
}
