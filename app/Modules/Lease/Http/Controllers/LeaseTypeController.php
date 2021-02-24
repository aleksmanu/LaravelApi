<?php
namespace App\Modules\Lease\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Lease\Http\Requests\LeaseTypes\LeaseTypeStoreRequest;
use App\Modules\Lease\Http\Requests\LeaseTypes\LeaseTypeUpdateRequest;
use App\Modules\Lease\Repositories\LeaseTypeRepository;

class LeaseTypeController extends Controller
{
    protected $lease_type_repository;

    public function __construct(LeaseTypeRepository $leaseTypeRepository)
    {
        $this->lease_type_repository = $leaseTypeRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response($this->lease_type_repository->getLeaseTypes());
    }

    /**
     * @param LeaseTypeStoreRequest $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function store(LeaseTypeStoreRequest $request)
    {
        $validated_data = $request->validated();
        return response($this->lease_type_repository->storeLeaseType($validated_data));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        return response($this->lease_type_repository->getLeaseType($id));
    }

    /**
     * @param LeaseTypeUpdateRequest $request
     * @param int $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function update(LeaseTypeUpdateRequest $request, int $id)
    {
        $validated_data = $request->validated();
        return response($this->lease_type_repository->updateLeaseType($id, $validated_data));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id)
    {
        return response($this->lease_type_repository->deleteLeaseType($id));
    }
}
