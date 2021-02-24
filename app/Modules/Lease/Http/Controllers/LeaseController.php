<?php
namespace App\Modules\Lease\Http\Controllers;

use App\Modules\Common\Http\Requests\Notes\NoteStoreRequest;
use App\Modules\Common\Services\NoteService;
use App\Modules\Common\Traits\HasNotesController;
use App\Modules\Core\Library\EloquentHelper;
use App\Modules\Edits\Http\Requests\GetEditAuditTrailRequest;
use App\Modules\Edits\Models\EditBatchType;
use App\Modules\Edits\Models\ReviewStatus;
use App\Modules\Edits\Services\EditBatchService;
use App\Modules\Lease\Http\Requests\Leases\LeaseIndexRequest;
use App\Modules\Lease\Http\Requests\Leases\LeaseStoreRequest;
use App\Modules\Lease\Http\Requests\Leases\LeaseUpdateRequest;
use App\Modules\Lease\Models\Lease;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Modules\Attachments\Traits\ControllerHasAttachments;
use App\Modules\Lease\Http\Requests\Leases\LeaseDatatableRequest;
use App\Modules\Lease\Repositories\LeaseRepository;

class LeaseController extends Controller
{
    use ControllerHasAttachments;
    use HasNotesController;
    
    /**
     * @var LeaseRepository
     */
    protected $lease_repository;

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
     * @var Lease
     */
    protected $model;

    /**
     * LeaseController constructor.
     * @param LeaseRepository $leaseRepository
     * @param EditBatchService $edit_service
     * @param NoteService $note_service
     * @param Lease $model
     */
    public function __construct(
        LeaseRepository $leaseRepository,
        EditBatchService $edit_service,
        NoteService $note_service,
        Lease $model
    ) {
        $this->lease_repository = $leaseRepository;
        $this->attachable_repository = $leaseRepository;
        $this->edit_service     = $edit_service;
        $this->note_service     = $note_service;
        $this->model = $model;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(LeaseIndexRequest $request)
    {
        $valid = $request->validated();

        return response($this->lease_repository->getLeases($valid));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function find(Request $request)
    {
        return response(
            $this->lease_repository->find($request->input('ids'))
        );
    }

    /**
     * @param LeaseStoreRequest $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function store(LeaseStoreRequest $request)
    {
        $validated_data = $request->validated();
        return response($this->lease_repository->storeLease($validated_data));
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        return response($this->lease_repository->getLease($id));
    }

    /**
     * @param LeaseUpdateRequest $request
     * @param int $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     * @throws \Exception
     * @throws \Throwable
     */
    public function update(LeaseUpdateRequest $request, int $id)
    {

        $lease = \DB::transaction(function () use ($id, $request) {

            $validated_data = $request->validated();
            $lease          = $this->lease_repository->getLease($id);

            if (!Auth::user()->canByPassEdit()) {
                $result = $this->edit_service->makeBatch($validated_data, EditBatchType::EDIT, $lease);

                if ($result) {
                    $lease = $this->lockLease($id);
                }
            } else {
                $lease = $this->lease_repository->updateLease($id, $validated_data);
            }
            return $lease;
        });

        return response($lease);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id)
    {
        return response($this->lease_repository->deleteLease($id));
    }

    public function dataTable(LeaseDatatableRequest $request)
    {
        return $this->lease_repository->list(
            $request->validated(),
            $request->sort_column ?? config('misc.dataTable.defaultSortColumn'),
            $request->sort_order ?? config('misc.dataTable.defaultSortOrder'),
            $request->limit ?? config('misc.dataTable.defaultPerPage'),
            intval($request->offset)
        );
    }

    /**
     * @param  $request
     * @return bool
     */
    public function getEditAuditTrail(GetEditAuditTrailRequest $request, $id)
    {

        $week_ending_date = Carbon::parse($request->week_ending_date);

        $entity = $this->lease_repository->getLease($id);

        return $this->edit_service->getEntityAuditTrail($entity, $week_ending_date);
    }

    /**
     * @return mixed
     */
    public function getEditable()
    {
        $model = $this->lease_repository->getModelObject();
        return $model->getEditable();
    }

    /**
     * @param $id
     * @return mixed
     * @throws \Exception
     * @throws \Throwable
     */
    public function flag($id)
    {
        $lease = $this->lease_repository->getLease($id);

        return $this->edit_service->makeBatch($lease->toArray(), EditBatchType::FLAG, $lease);
    }

    /**
     * @param $id
     * @return Lease
     */
    private function lockLease($id)
    {
        return $this->lease_repository->updateLease($id, [
            'locked_at'         => Carbon::now(),
            'locked_by_user_id' => Auth::user()->id,
            'review_status_id'  => EloquentHelper::getRecordIdBySlug(ReviewStatus::class, ReviewStatus::IN_REVIEW)]);
    }
}
