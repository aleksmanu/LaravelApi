<?php
namespace App\Modules\Lease\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Lease\Http\Requests\RentFrequencies\RentFrequencyStoreRequest;
use App\Modules\Lease\Http\Requests\RentFrequencies\RentFrequencyUpdateRequest;
use App\Modules\Lease\Repositories\RentFrequencyRepository;

class RentFrequencyController extends Controller
{
    protected $rent_frequency_repository;

    public function __construct(RentFrequencyRepository $rentFrequencyRepository)
    {
        $this->rent_frequency_repository = $rentFrequencyRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response($this->rent_frequency_repository->getRentFrequencies());
    }

    /**
     * @param RentFrequencyStoreRequest $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function store(RentFrequencyStoreRequest $request)
    {
        $validated_data = $request->validated();
        return response($this->rent_frequency_repository->storeRentFrequency($validated_data));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        return response($this->rent_frequency_repository->getRentFrequency($id));
    }

    /**
     * @param RentFrequencyUpdateRequest $request
     * @param $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function update(RentFrequencyUpdateRequest $request, $id)
    {
        $validated_data = $request->validated();
        return response($this->rent_frequency_repository->updateRentFrequency($id, $validated_data));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return response($this->rent_frequency_repository->deleteRentFrequency($id));
    }
}
