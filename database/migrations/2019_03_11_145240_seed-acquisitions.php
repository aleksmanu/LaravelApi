<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SeedAcquisitions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $checklist = [
            'id' => 1,
            'is_template' => true,
            'name' => 'Default Workflow',
        ];

        $steps = [
            ['acquisition_checklist_id' => 1, 'id' => 1, 'acquisition_step_group_id' => 1, 'order' => 1, 'label' => 'MS1 - Instructions Issued', 'duration_days' => 5, 'type' => 'doc:PDF File', 'mandatory' => true],
            ['acquisition_checklist_id' => 1, 'id' => 2, 'acquisition_step_group_id' => 1, 'order' => 2, 'label' => 'Site Search', 'duration_days' => 5,  'type' => 'text:Notes', 'mandatory' => false],
            ['acquisition_checklist_id' => 1, 'id' => 3, 'acquisition_step_group_id' => 1, 'order' => 3, 'label' => 'MS2 - Pop Nomination', 'duration_days' => 20,  'type' => 'text:Notes', 'mandatory' => false],
            ['acquisition_checklist_id' => 1, 'id' => 4, 'acquisition_step_group_id' => 2, 'order' => 4, 'label' =>  'Wayleave Plan', 'duration_days' => 10,  'type' => 'text:Notes', 'mandatory' => false],
            ['acquisition_checklist_id' => 1, 'id' => 5, 'acquisition_step_group_id' => 2, 'order' => 5, 'label' =>  'Power Check', 'duration_days' => 2,  'type' => 'text:Notes', 'mandatory' => false],
            ['acquisition_checklist_id' => 1, 'id' => 6, 'acquisition_step_group_id' => 2, 'order' => 6, 'label' =>  'Schedule of Condition', 'duration_days' => 30,  'type' => 'text:Notes', 'mandatory' => false],
            ['acquisition_checklist_id' => 1, 'id' => 7, 'acquisition_step_group_id' => 3, 'order' => 7, 'label' => 'Design Visit', 'duration_days' => 10, 'type' => 'doc:PDF File', 'mandatory' => true],
            ['acquisition_checklist_id' => 1, 'id' => 8, 'acquisition_step_group_id' => 3, 'order' => 8, 'label' => 'Drawings Issued', 'duration_days' => 5, 'type' => 'doc:PDF File', 'mandatory' => true],
            ['acquisition_checklist_id' => 1, 'id' => 9, 'acquisition_step_group_id' => 3, 'order' => 9, 'label' => 'Drawings Approved', 'duration_days' => 10, 'type' => 'doc:PDF File', 'mandatory' => true],
            ['acquisition_checklist_id' => 1, 'id' => 10, 'acquisition_step_group_id' => 4, 'order' => 10, 'label' => 'MS3 Planning Submitted', 'duration_days' => 5,  'type' => 'text:Notes', 'mandatory' => false],
            //['acquisition_checklist_id' => 1, 'id' => 11, 'acquisition_step_group_id' => 4, 'order' => 11, 'label' => 'Planning Type', 'duration_days' => 10,  'type' => 'text:Notes', 'mandatory' => false],
            //['acquisition_checklist_id' => 1, 'id' => 12, 'acquisition_step_group_id' => 4, 'order' => 12, 'label' => 'Planning Application No.', 'duration_days' => 5,  'type' => 'text:Notes', 'mandatory' => false],
            ['acquisition_checklist_id' => 1, 'id' => 13, 'acquisition_step_group_id' => 4, 'order' => 13, 'label' => 'Planning Validation', 'duration_days' => 10,  'type' => 'text:Notes', 'mandatory' => false],
            ['acquisition_checklist_id' => 1, 'id' => 14, 'acquisition_step_group_id' => 4, 'order' => 14, 'label' => 'Consultation Date', 'duration_days' => 5,  'type' => 'text:Notes', 'mandatory' => false],
            ['acquisition_checklist_id' => 1, 'id' => 15, 'acquisition_step_group_id' => 4, 'order' => 15, 'label' => 'MS4 Planning Determination', 'duration_days' => 10,  'type' => 'text:Notes', 'mandatory' => false],
            ['acquisition_checklist_id' => 1, 'id' => 16, 'acquisition_step_group_id' => 5, 'order' => 16, 'label' => 'Heads of Terms Issued', 'duration_days' => 18,  'type' => 'text:Notes', 'mandatory' => false],
            ['acquisition_checklist_id' => 1, 'id' => 17, 'acquisition_step_group_id' => 5, 'order' => 17, 'label' => 'MS5 Heads of Terms Approved', 'duration_days' => 30, 'type' => 'doc:PDF File', 'mandatory' => true],
            ['acquisition_checklist_id' => 1, 'id' => 18, 'acquisition_step_group_id' => 5, 'order' => 18, 'label' => 'MS6 Instruct Solicitors', 'duration_days' => 25, 'type' => 'doc:PDF File', 'mandatory' => true],
            ['acquisition_checklist_id' => 1, 'id' => 19, 'acquisition_step_group_id' => 5, 'order' => 19, 'label' => 'MS7 Legal Completion', 'duration_days' => 30, 'type' => 'doc:PDF File', 'mandatory' => true],
            ['acquisition_checklist_id' => 1, 'id' => 20, 'acquisition_step_group_id' => 5, 'order' => 20, 'label' => 'Handover Pack', 'duration_days' => 20, 'type' => 'doc:PDF File', 'mandatory' => true],
        ];
        $groups = [
            ['id' => 1, 'name' => 'Survey', 'order' => 1],
            ['id' => 2, 'name' => 'Miscellaneous ', 'order' => 2],
            ['id' => 3, 'name' => 'Design', 'order' => 3],
            ['id' => 4, 'name' => 'Planning', 'order' => 4],
            ['id' => 5, 'name' => 'Legals', 'order' => 5],
        ];

        DB::transaction(function () use ($checklist, $groups, $steps) {
            DB::table('acquisition_checklists')->insert([$checklist]);
            DB::table('acquisition_step_groups')->insert($groups);
            DB::table('acquisition_steps')->insert($steps);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::delete('delete from acquisition_step_groups where id < 6');
        DB::delete('delete from acquisition_steps where id < 21');
        DB::delete('delete from acquisition_checklists where id = 1');
    }
}
