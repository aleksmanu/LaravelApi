<?php
namespace App\Modules\Common\Http\Controllers;

use App\Modules\Common\Http\Requests\Addresses\AddressStoreRequest;
use App\Modules\Common\Http\Requests\Addresses\AddressUpdateRequest;
use App\Modules\Common\Repositories\AddressRepository;
use App\Http\Controllers\Controller;

class AddressController extends Controller
{
    protected $address_repository;

    public function __construct(AddressRepository $addressRepository)
    {
        $this->address_repository = $addressRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response($this->address_repository->getAddresses());
    }


    /**
     * @param AddressStoreRequest $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function store(AddressStoreRequest $request)
    {
        $validated_data = $request->validated();
        return response($this->address_repository->storeAddress($validated_data));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        return response($this->address_repository->getAddress($id));
    }

    /**
     * @param AddressUpdateRequest $request
     * @param $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function update(AddressUpdateRequest $request, $id)
    {
        $validated_data = $request->validated();
        return response($this->address_repository->updateAddress($id, $validated_data));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id)
    {
        return response($this->address_repository->deleteAddress($id));
    }
}
