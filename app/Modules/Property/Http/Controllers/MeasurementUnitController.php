<?php
namespace App\Modules\Property\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Property\Http\Requests\MeasurementUnits\MeasurementUnitStoreRequest;
use App\Modules\Property\Http\Requests\MeasurementUnits\MeasurementUnitUpdateRequest;

class MeasurementUnitController extends Controller
{
    protected $measurement_unit_repository;

    public function __construct(MeasurementUnitRepository $measurementUnitRepository)
    {
        $this->measurement_unit_repository = $measurementUnitRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response($this->measurement_unit_repository->getMeasurementUnits());
    }

    /**
     * @param MeasurementUnitStoreRequest $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function store(MeasurementUnitStoreRequest $request)
    {
        $validated_data = $request->validated();
        return response($this->measurement_unit_repository->storeMeasurementUnit($validated_data));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        return response($this->measurement_unit_repository->getMeasurementUnit($id));
    }

    /**
     * @param MeasurementUnitUpdateRequest $request
     * @param int $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function update(MeasurementUnitUpdateRequest $request, int $id)
    {
        $validated_data = $request->validated();
        return response($this->measurement_unit_repository->updateMeasurementUnit($id, $validated_data));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id)
    {
        return response($this->measurement_unit_repository->deleteMeasurementUnit($id));
    }
}
