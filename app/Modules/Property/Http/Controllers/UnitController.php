<?php

namespace App\Modules\Property\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\Attachments\Traits\ControllerHasAttachments;
use App\Modules\Common\Services\NoteService;
use App\Modules\Common\Traits\HasNotesController;
use App\Modules\Core\Library\EloquentHelper;
use App\Modules\Edits\Http\Requests\GetEditAuditTrailRequest;
use App\Modules\Edits\Models\EditBatchType;
use App\Modules\Edits\Models\ReviewStatus;
use App\Modules\Edits\Services\EditBatchService;
use App\Modules\Property\Http\Requests\Units\UnitStoreRequest;
use App\Modules\Property\Http\Requests\Units\UnitUpdateRequest;
use App\Modules\Property\Models\Unit;
use App\Modules\Property\Repositories\UnitRepository;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Response;

class UnitController extends Controller
{
    use ControllerHasAttachments;
    use HasNotesController;

    /**
     * @var UnitRepository
     */
    protected $unit_repository;

    /**
     * @var EditBatchService
     */
    protected $edit_service;

    /**
     * @var NoteService
     */
    protected $note_service;

    /**
     * @var attachable_repository
     */
    protected $attachable_repository;

    /**
     * @var Unit
     */
    protected $model;

    /**
     * UnitController constructor.
     * @param Unit $unitModel
     * @param UnitRepository $unitRepository
     * @param EditBatchService $edit_service
     * @param NoteService $note_service
     */
    public function __construct(
        Unit $unitModel,
        UnitRepository $unitRepository,
        EditBatchService $edit_service,
        NoteService $note_service
    ) {
        $this->model = $unitModel;
        $this->unit_repository = $unitRepository;
        $this->attachable_repository = $unitRepository;
        $this->edit_service = $edit_service;
        $this->note_service = $note_service;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response($this->unit_repository->getUnits());
    }

    /**
     * @param UnitStoreRequest $request
     * @return ResponseFactory|Response
     */
    public function store(UnitStoreRequest $request)
    {
        $validated_data = $request->validated();
        return response($this->unit_repository->storeUnit($validated_data));
    }

    /**
     * @param Request $request
     * @return ResponseFactory|Response
     */
    public function find(Request $request)
    {
        return response(
            $this->unit_repository->findUnits($request->input('ids'))
        );
    }

    /**
     * @param Request $request
     * @return ResponseFactory|Response
     */
    public function dashFind(Request $request)
    {
        return response(
            $this->unit_repository->dashOptimizedFind($request->input('ids'))
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        return response($this->unit_repository->getUnit($id));
    }

    /**
     * @param UnitUpdateRequest $request
     * @param int $id
     * @return ResponseFactory|Response
     * @throws \Exception
     * @throws \Throwable
     */
    public function update(UnitUpdateRequest $request, int $id)
    {

        $unit = \DB::transaction(function () use ($request, $id) {

            $validated_data = $request->validated();
            $unit           = $this->unit_repository->getUnit($id);

            if (!Auth::user()->canByPassEdit()) {
                $result = $this->edit_service->makeBatch($validated_data, EditBatchType::EDIT, $unit);

                if ($result) {
                    $this->lockUnit($id);
                }
            } else {
                $unit = $this->unit_repository->updateUnit($id, $validated_data);
            }
            return $unit;
        });
        return response($unit);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id)
    {
        return response($this->unit_repository->deleteUnit($id));
    }

//    /**
//     * @param NoteStoreRequest $request
//     * @param $id
//     * @return mixed
//     * @throws \Illuminate\Validation\ValidationException
//     */
//    public function storeNote(NoteStoreRequest $request, $id)
//    {
//        return $this->note_service->make([
//            'user_id'     => Auth::user()->id,
//            'entity_id'   => $id,
//            'entity_type' => $this->unit_repository->getModelClass(),
//            'note'        => $request->note
//        ]);
//    }

    /**
     * @param  $request
     * @return bool
     */
    public function getEditAuditTrail(GetEditAuditTrailRequest $request, $id)
    {

        $week_ending_date = Carbon::parse($request->week_ending_date);

        $entity = $this->unit_repository->getUnit($id);

        return $this->edit_service->getEntityAuditTrail($entity, $week_ending_date);
    }

    public function dataTable(Request $request)
    {
        return response($this->unit_repository->getUnitsDataTable(
            $request->sort_column ?? config('misc.dataTable.defaultSortColumn'),
            $request->sort_order ?? config('misc.dataTable.defaultSortOrder'),
            intval($request->offset),
            $request->limit ?? config('misc.dataTable.defaultPerPage'),
            intval($request->property_id),
            intval($request->property_manager_id),
            intval($request->client_account_id),
            intval($request->portfolio_id),
            $request->property_name_partial
        ));
    }

    /**
     * @return mixed
     */
    public function getEditable()
    {

        $model_object = $this->unit_repository->getModelObject();
        return $model_object->getEditable();
    }

    /**
     * @param $id
     * @return Unit
     * @throws \Exception
     * @throws \Throwable
     */
    public function flag($id)
    {

        $unit = $this->unit_repository->getUnit($id);
        $this->edit_service->makeBatch($unit->toArray(), EditBatchType::FLAG, $unit);

        return $this->lockUnit($id);
    }

    /**
     * @param $id
     * @return Unit
     */
    private function lockUnit($id)
    {
        return $this->unit_repository->updateUnit($id, [
            'locked_at'         => Carbon::now(),
            'locked_by_user_id' => Auth::user()->id,
            'review_status_id'  => EloquentHelper::getRecordIdBySlug(ReviewStatus::class, ReviewStatus::IN_REVIEW)
        ]);
    }
}
