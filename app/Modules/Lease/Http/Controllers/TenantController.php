<?php
namespace App\Modules\Lease\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Common\Http\Requests\Notes\NoteStoreRequest;
use App\Modules\Common\Services\NoteService;
use App\Modules\Core\Library\EloquentHelper;
use App\Modules\Edits\Http\Requests\GetEditAuditTrailRequest;
use App\Modules\Edits\Models\EditBatchType;
use App\Modules\Edits\Models\ReviewStatus;
use App\Modules\Edits\Services\EditBatchService;
use App\Modules\Lease\Http\Requests\Tenants\TenantStoreRequest;
use App\Modules\Lease\Http\Requests\Tenants\TenantUpdateRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Modules\Attachments\Traits\ControllerHasAttachments;
use App\Modules\Lease\Repositories\TenantRepository;

class TenantController extends Controller
{
    use ControllerHasAttachments;
    /**
     * @var TenantRepository
     */
    protected $tenant_repository;

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
     * TenantController constructor.
     * @param TenantRepository $tenantRepository
     * @param EditBatchService $edit_service
     * @param NoteService $note_service
     */
    public function __construct(
        TenantRepository $tenantRepository,
        EditBatchService $edit_service,
        NoteService $note_service
    ) {
        $this->tenant_repository = $tenantRepository;
        $this->edit_service = $edit_service;
        $this->note_service = $note_service;
        $this->attachable_repository = $tenantRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response($this->tenant_repository->getTenants());
    }

    /**
     * @param TenantStoreRequest $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function store(TenantStoreRequest $request)
    {
        $validated_data = $request->validated();
        return response($this->tenant_repository->storeTenant($validated_data));
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        return response($this->tenant_repository->getTenant($id));
    }

    /**
     * @param TenantUpdateRequest $request
     * @param int $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     * @throws \Exception
     * @throws \Throwable
     */
    public function update(TenantUpdateRequest $request, int $id)
    {

        $tenant = \DB::transaction(function () use ($request, $id) {

            $validated_data = $request->validated();
            $tenant         = $this->tenant_repository->getTenant($id);

            if (!Auth::user()->canByPassEdit()) {
                $result = $this->edit_service->makeBatch($validated_data, EditBatchType::EDIT, $tenant);

                if ($result) {
                    $tenant = $this->lockTenant($id);
                }
            } else {
                $tenant = $this->tenant_repository->updateTenant($id, $validated_data);
            }
            return $tenant;
        });
        return response($tenant);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id)
    {
        return response($this->tenant_repository->deleteTenant($id));
    }

    /**
     * @param NoteStoreRequest $request
     * @param $id
     * @return mixed
     * @throws \Illuminate\Validation\ValidationException
     */
    public function storeNote(NoteStoreRequest $request, $id)
    {
        return $this->note_service->make([
            'user_id'     => Auth::user()->id,
            'entity_id'   => $id,
            'entity_type' => $this->tenant_repository->getModelClass(),
            'note'        => $request->note
        ]);
    }

    /**
     * @param  $request
     * @return bool
     */
    public function getEditAuditTrail(GetEditAuditTrailRequest $request, $id)
    {

        $week_ending_date = Carbon::parse($request->week_ending_date);

        $entity = $this->tenant_repository->getTenant($id);

        return $this->edit_service->getEntityAuditTrail($entity, $week_ending_date);
    }

    /**
     * @return mixed
     */
    public function getEditable()
    {
        $model = $this->tenant_repository->getModelObject();
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

        $lease = $this->tenant_repository->getLease($id);

        return $this->edit_service->makeBatch($lease->toArray(), EditBatchType::FLAG, $lease);
    }

    /**
     * @param $id
     * @return \App\Modules\Lease\Models\Tenant
     */
    private function lockTenant($id)
    {

        return $this->tenant_repository->updateTenant($id, [
            'locked_at'         => Carbon::now(),
            'locked_by_user_id' => Auth::user()->id,
            'review_status_id'  => EloquentHelper::getRecordIdBySlug(ReviewStatus::class, ReviewStatus::IN_REVIEW)]);
    }
}
