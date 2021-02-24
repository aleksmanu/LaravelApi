<?php

namespace App\Modules\Property\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Property\Http\Requests\PropertyUses\PropertyUseStoreRequest;
use App\Modules\Property\Http\Requests\PropertyUses\PropertyUseUpdateRequest;
use App\Modules\Property\Repositories\PropertyUseRepository;

class PropertyUseController extends Controller
{
    /**
     * @var PropertyUseRepository
     */
    protected $property_use_repository;

    /**
     * PropertyUseController constructor.
     * @param IPropertyUseRePropertyUseRepositorypository $propertyUseRepository
     */
    public function __construct(PropertyUseRepository $propertyUseRepository)
    {
        $this->property_use_repository = $propertyUseRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response($this->property_use_repository->getPropertyUses());
    }

    /**
     * @param PropertyUseStoreRequest $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function store(PropertyUseStoreRequest $request)
    {
        $validated_data = $request->validated();
        return response($this->property_use_repository->storePropertyUse($validated_data));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        return response($this->property_use_repository->getPropertyUse($id));
    }

    /**
     * @param PropertyUseUpdateRequest $request
     * @param $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function update(PropertyUseUpdateRequest $request, $id)
    {
        $validated_data = $request->validated();
        return response($this->property_use_repository->updatePropertyUse($id, $validated_data));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return response($this->property_use_repository->deletePropertyUse($id));
    }
}
