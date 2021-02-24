<?php
namespace App\Modules\Edits\Tests\Unit\EditBatchController;

use App\Modules\Core\Library\EloquentHelper;
use App\Modules\Edits\Models\EditBatch;
use App\Modules\Edits\Models\EditStatus;
use App\Modules\TestFramework\Classes\BaseTestClass;

class SubmitTest extends BaseTestClass
{
    /**
     * @var EditBatch
     */
    private $edit_batch;

    /**
     * @throws \Exception
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->edit_batch = EditBatch::first();
    }

    /**
     * @see EditBatchController::submit()
     * @test
     */
    public function test()
    {
        if ($this->edit_batch) {
            foreach ($this->edit_batch->edits as $edit) {
                $edit->update([
                    'edit_status_id' => EloquentHelper::getRecordIdBySlug(
                        EditStatus::class,
                        EditStatus::APPROVED
                    )
                ]);
            }
            $result = $this->apiAs($this->user, 'POST', '/api/edits/edit-batches/submit/' . $this->edit_batch->id, []);
            $result->assertSuccessful();
        } else {
            $this->markTestSkipped('No edit data to test against');
        }
    }
}
