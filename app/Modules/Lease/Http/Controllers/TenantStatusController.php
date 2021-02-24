<?php
namespace App\Modules\Lease\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Core\Library\StringHelper;
use App\Modules\Lease\Http\Requests\TenantStatuses\TenantStatusStoreRequest;
use App\Modules\Lease\Http\Requests\TenantStatuses\TenantStatusUpdateRequest;
use App\Modules\Lease\Repositories\TenantStatusRepository;

class TenantStatusController extends Controller
{
    protected $tenant_status_repository;

    public function __construct(TenantStatusRepository $tenantStatusRepository)
    {
        $this->tenant_status_repository = $tenantStatusRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response($this->tenant_status_repository->getTenantStatuses());
    }

    /**
     * @param TenantStatusStoreRequest $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function store(TenantStatusStoreRequest $request)
    {
        $validated_data = $request->validated();
        $validated_data['slug'] = StringHelper::slugify($validated_data['name']);
        return response($this->tenant_status_repository->storeTenantStatus($validated_data));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        return response($this->tenant_status_repository->getTenantStatus($id));
    }

    /**
     * @param TenantStatusUpdateRequest $request
     * @param int $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function update(TenantStatusUpdateRequest $request, int $id)
    {
        $validated_data = $request->validated();
        $validated_data['slug'] = StringHelper::slugify($validated_data['name']);
        return response($this->tenant_status_repository->updateTenantStatus($id, $validated_data));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id)
    {
        return response($this->tenant_status_repository->deleteTenantStatus($id));
    }
}
