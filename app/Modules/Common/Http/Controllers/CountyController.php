<?php
namespace App\Modules\Common\Http\Controllers;

use App\Modules\Common\Http\Requests\Counties\CountyStoreRequest;
use App\Modules\Common\Http\Requests\Counties\CountyUpdateRequest;
use App\Modules\Common\Repositories\CountyRepository;

use App\Http\Controllers\Controller;

class CountyController extends Controller
{
    protected $county_repository;

    public function __construct(CountyRepository $countyRepository)
    {
        $this->county_repository = $countyRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response($this->county_repository->getCounties());
    }


    /**
     * @param CountyStoreRequest $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function store(CountyStoreRequest $request)
    {
        $validated_data = $request->validated();
        return response($this->county_repository->storeCounty($validated_data));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        return response($this->county_repository->getCounty($id));
    }

    /**
     * @param CountyUpdateRequest $request
     * @param $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function update(CountyUpdateRequest $request, $id)
    {
        $validated_data = $request->validated();
        return response($this->county_repository->updateCounty($id, $validated_data));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id)
    {
        return response($this->county_repository->deleteCounty($id));
    }
}
