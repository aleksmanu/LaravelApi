<?php
namespace App\Modules\Acquisition\Repositories;

use App\Modules\Common\Classes\AddressDataHelper;

use App\Modules\Acquisition\Models\Step;
use App\Modules\Core\Classes\Repository;
use App\Modules\Attachments\Traits\RepoHasAttachments;
use Illuminate\Support\Facades\DB;

class StepRepository extends Repository
{
    use RepoHasAttachments;

    public function __construct(Step $model)
    {
        $this->model = $model;
    }

    public function update($step, $data)
    {
        DB::beginTransaction();
        try {
            $affectedStep = Step::findOrFail($step);
            $affectedStep->update($data);

            if (!array_key_exists('forecast_for', $data) && !array_key_exists('target_date', $data)) {
                $checklist = $affectedStep->checklist;
                $steps = $checklist->steps;
                foreach ($steps as $step) {
                    if ($step->completed_on || $step->depends_on_step_order_number === null) {
                        continue;
                    }

                    $dependsOnStep = $checklist->steps()->where(
                        'order',
                        '=',
                        $step->depends_on_step_order_number
                    )->get()[0];

                    if ($dependsOnStep->completed_on) {
                        $step->start_on = $dependsOnStep->completed_on;
                    } else {
                        $step->start_on = $dependsOnStep->forecast_for;
                    }
                    $step->forecast_for = $step->start_on->addDays($step->duration_days);

                    $step->save();
                }
            }
        } catch (\Exception $e) {
            DB::rollback();
            return $e;
        }
        DB::commit();
        return $step;
    }

    public function get($step)
    {
        return Step::findOrFail($step);
    }
}
