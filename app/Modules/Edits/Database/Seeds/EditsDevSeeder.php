<?php

namespace App\Modules\Edits\Database\Seeds;

use Illuminate\Database\Seeder;

class EditsDevSeeder extends Seeder
{

    public function run()
    {

        $this->createReviewStatuses();
        $this->createEditStatuses();
        $this->createEditBatchTypes();
    }

    private function createReviewStatuses()
    {

        $data = [
            ['name' => 'Never Reviewed', 'slug' => 'never_reviewed'],
            ['name' => 'In Review', 'slug' => 'in_review'],
            ['name' => 'Reviewed and Updated', 'slug' => 'reviewed'],
        ];
        \App\Modules\Edits\Models\ReviewStatus::insert($data);
    }

    private function createEditStatuses()
    {
        $data = [
            ['name' => 'Pending', 'slug' => 'pending'],
            ['name' => 'Approved', 'slug' => 'approved'],
            ['name' => 'Rejected', 'slug' => 'rejected'],
        ];
        \App\Modules\Edits\Models\EditStatus::insert($data);
    }

    private function createEditBatchTypes()
    {
        $data = [
            ['name' => 'Edit', 'slug' => 'edit'],
            ['name' => 'Create', 'slug' => 'slug'],
            ['name' => 'Flag', 'slug' => 'flag'],
        ];
        \App\Modules\Edits\Models\EditBatchType::insert($data);
    }
}
