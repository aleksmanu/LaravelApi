<?php
namespace App\Modules\Property\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Property\Repositories\PropertyStatusRepository;
use App\Modules\Property\Http\Requests\PropertyStatuses\PropertyStatusStoreRequest;
use App\Modules\Property\Http\Requests\PropertyStatuses\PropertyStatusUpdateRequest;

class PropertyStatusController extends Controller
{
    protected $property_status_repository;

    public function __construct(PropertyStatusRepository $propertyStatusRepository)
    {
        $this->property_status_repository = $propertyStatusRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response($this->property_status_repository->getPropertyStatuses());
    }

    /**
     * @param PropertyStatusStoreRequest $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function store(PropertyStatusStoreRequest $request)
    {
        $validated_data = $request->validated();
        return response($this->property_status_repository->storePropertyStatus($validated_data));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        return response($this->property_status_repository->getPropertyStatus($id));
    }

    /**
     * @param PropertyStatusUpdateRequest $request
     * @param $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function update(PropertyStatusUpdateRequest $request, $id)
    {
        $validated_data = $request->validated();
        return response($this->property_status_repository->updatePropertyStatus($id, $validated_data));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return response($this->property_status_repository->deletePropertyStatus($id));
    }
}
