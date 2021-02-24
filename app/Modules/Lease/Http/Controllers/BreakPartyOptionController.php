<?php
namespace App\Modules\Lease\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Lease\Http\Requests\BreakPartyOptions\BreakPartyOptionStoreRequest;
use App\Modules\Lease\Http\Requests\BreakPartyOptions\BreakPartyUpdateRequest;
use App\Modules\Lease\Repositories\BreakPartyOptionRepository;

class BreakPartyOptionController extends Controller
{
    protected $break_party_option_repository;

    public function __construct(BreakPartyOptionRepository $breakPartyOptionRepository)
    {
        $this->break_party_option_repository = $breakPartyOptionRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response($this->break_party_option_repository->getBreakPartyOptions());
    }

    /**
     * @param BreakPartyOptionStoreRequest $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function store(BreakPartyOptionStoreRequest $request)
    {
        $validated_data = $request->validated();
        return response($this->break_party_option_repository->storeBreakPartyOption($validated_data));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        return response($this->break_party_option_repository->getBreakPartyOption($id));
    }

    /**
     * @param BreakPartyUpdateRequest $request
     * @param int $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function update(BreakPartyUpdateRequest $request, int $id)
    {
        $validated_data = $request->validated();
        return response($this->break_party_option_repository->updateBreakPartyOption($id, $validated_data));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id)
    {
        return response($this->break_party_option_repository->deleteBreakPartyOption($id));
    }
}
