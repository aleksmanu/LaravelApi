<?php
namespace App\Modules\Property\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Property\Http\Requests\LocationTypes\LocationTypeStoreRequest;
use App\Modules\Property\Http\Requests\LocationTypes\LocationTypeUpdateRequest;
use App\Modules\Property\Repositories\LocationTypeRepository;

class LocationTypeController extends Controller
{
    protected $location_type_repository;

    public function __construct(LocationTypeRepository $locationTypeRepository)
    {
        $this->location_type_repository = $locationTypeRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response($this->location_type_repository->getLocationTypes());
    }

    /**
     * @param LocationTypeStoreRequest $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function store(LocationTypeStoreRequest $request)
    {
        $validated_data = $request->validated();
        return response($this->location_type_repository->storeLocationType($validated_data));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        return response($this->location_type_repository->getLocationType($id));
    }

    /**
     * @param LocationTypeUpdateRequest $request
     * @param $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function update(LocationTypeUpdateRequest $request, $id)
    {
        $validated_data = $request->validated();
        return response($this->location_type_repository->updateLocationType($id, $validated_data));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return response($this->location_type_repository->deleteLocationType($id));
    }
}
