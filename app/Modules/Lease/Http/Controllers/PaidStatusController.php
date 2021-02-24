<?php
namespace App\Modules\Lease\Http\Controllers;

use App\Modules\Lease\Http\Requests\PaidStatuses\PaidStatusStoreRequest;
use App\Modules\Lease\Http\Requests\PaidStatuses\PaidStatusUpdateRequest;

use App\Http\Controllers\Controller;
use App\Modules\Lease\Repositories\PaidStatusRepository;

class PaidStatusController extends Controller
{
    protected $paid_status_repository;

    public function __construct(PaidStatusRepository $paidStatusRepository)
    {
        $this->paid_status_repository = $paidStatusRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response($this->paid_status_repository->getPaidStatuses());
    }

    /**
     * @param PaidStatusStoreRequest $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function store(PaidStatusStoreRequest $request)
    {
        $validated_data = $request->validated();
        return response($this->paid_status_repository->storePaidStatus($validated_data));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        return response($this->paid_status_repository->getPaidStatus($id));
    }

    /**
     * @param PaidStatusUpdateRequest $request
     * @param int $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function update(PaidStatusUpdateRequest $request, int $id)
    {
        $validated_data = $request->validated();
        return response($this->paid_status_repository->updatePaidStatus($id, $validated_data));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id)
    {
        return response($this->paid_status_repository->deletePaidStatus($id));
    }
}
