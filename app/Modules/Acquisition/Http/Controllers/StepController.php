<?php
namespace App\Modules\Acquisition\Http\Controllers;

use App\Modules\Acquisition\Models\Step;

use App\Modules\Common\Contracts\IControlsTimeline;
use App\Modules\Common\Services\NoteService;
use App\Modules\Common\Traits\HasNotesController;
use App\Modules\Common\Traits\HasTimelineControlsTrait;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\Modules\Acquisition\Repositories\StepRepository;
use App\Modules\Attachments\Traits\ControllerHasAttachments;

class StepController extends Controller implements IControlsTimeline
{
    use ControllerHasAttachments;
    use HasTimelineControlsTrait;
    use HasNotesController;
    
    protected $repository;
    protected $attachable_repository;
    protected $note_service;
    protected $model;

    public function __construct(StepRepository $stepRepository, NoteService $note_service, Step $model)
    {
        $this->repository = $stepRepository;
        $this->note_service = $note_service;
        $this->attachable_repository = $stepRepository;
        $this->model = $model;
    }

    public function getTimeline(int $id)
    {
        $step = $this->repository->get($id);
        return response($step->allTimelines());
    }

    public function show($step)
    {
        return response($this->repository->get($step));
    }

    public function update(Request $request, $step)
    {
        return response($this->repository->update($step, $request->all()));
    }
}
