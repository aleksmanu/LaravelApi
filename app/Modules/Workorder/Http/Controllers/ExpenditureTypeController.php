<?php

namespace App\Modules\Workorder\Http\Controllers;

use App\Modules\Workorder\Models\ExpenditureType;
use App\Modules\Workorder\Repositories\Interfaces\IExpenditureTypeRepository;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

class ExpenditureTypeController extends Controller
{
    protected $expenditure_type_repository;

    public function __construct(IExpenditureTypeRepository $expenditureType)
    {
        $this->expenditure_type_repository = $expenditureType;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response($this->expenditure_type_repository->list());
    }

    /**
     * @param ExpenditureTypeStoreRequest $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function store(ExpenditureTypeStoreRequest $request)
    {
        //TODO: Implement this in the new year
        return response([]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        return response($this->expenditure_type_repository->get($id));
    }

    /**
     * @param ExpenditureTypeUpdateRequest $request
     * @param $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function update(ExpenditureTypeUpdateRequest $request, $id)
    {
        //TODO: Implement this in the new year
        return response([$id]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //TODO: Implement this in the new year
        return response([$id]);
    }
}
