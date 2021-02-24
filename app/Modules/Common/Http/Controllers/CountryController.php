<?php

namespace App\Modules\Common\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Common\Http\Requests\Countries\CountryStoreRequest;
use App\Modules\Common\Http\Requests\Countries\CountryUpdateRequest;
use App\Modules\Common\Repositories\CountryRepository;

class CountryController extends Controller
{
    protected $country_repository;

    public function __construct(CountryRepository $countryRepository)
    {
        $this->country_repository = $countryRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response($this->country_repository->getCountries());
    }


    /**
     * @param CountryStoreRequest $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function store(CountryStoreRequest $request)
    {
        $validated_data = $request->validated();
        return response($this->country_repository->storeCountry($validated_data));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        return response($this->country_repository->getCountry($id));
    }

    /**
     * @param CountryUpdateRequest $request
     * @param $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function update(CountryUpdateRequest $request, $id)
    {
        $validated_data = $request->validated();
        return response($this->country_repository->updateCountry($id, $validated_data));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id)
    {
        return response($this->country_repository->deleteCountry($id));
    }
}
