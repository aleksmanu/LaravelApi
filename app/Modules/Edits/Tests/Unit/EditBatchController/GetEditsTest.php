<?php

namespace App\Modules\Edits\Tests\Unit\EditBatchController;

use App\Modules\Edits\Http\Controllers\EditBatchController;
use App\Modules\Edits\Models\EditBatch;
use App\Modules\TestFramework\Classes\BaseTestClass;

class GetEditsTest extends BaseTestClass
{

    /**
     * @see EditBatchController::getEdits()
     * @test
     */
    public function test()
    {

        $edit_batch = EditBatch::first();
        if ($edit_batch) {
            $result = $this->apiAs($this->user, 'GET', '/api/edits/edit-batches/get-edits/' . $edit_batch->id, []);

            $result->assertSuccessful();
            $result = $result->json();

            $this->assertSame($edit_batch->edits->count(), count($result['edits']));
        } else {
            $this->markTestSkipped('No edit data to test against');
        }
    }
}
