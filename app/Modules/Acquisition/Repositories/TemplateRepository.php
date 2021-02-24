<?php
namespace App\Modules\Acquisition\Repositories;

use App\Modules\Acquisition\Models\Checklist;
use App\Modules\Acquisition\Models\Step;
use App\Modules\Acquisition\Models\Acquisition;
use App\Modules\Acquisition\Models\Site;

class TemplateRepository
{
    public function __construct(Checklist $model)
    {
        $this->model = $model;
    }

    public function index()
    {
        return $this->model::where('is_template', true)
            ->with(['steps', 'steps.stepGroup'])
            ->get();
    }

    public function create(array $checklistData)
    {
        $templateSteps = [];
        foreach ($checklistData['steps'] as $step) {
            $newStep = Step::create($step);
            $templateSteps[] = $newStep;
        }
        $template = Checklist::create([
            'name' => $checklistData['name'],
            'is_template' => true,
        ]);
        $template->steps()->saveMany($templateSteps);
        $template->save();

        return $template;
    }

    public function get($id)
    {
        return $this->model->find($id);
    }
}
