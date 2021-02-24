<?php
namespace App\Modules\Workorder\Http\Controllers;

use App\Modules\Workorder\Models\Supplier;
use App\Modules\Workorder\Repositories\Interfaces\ISupplierRepository;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

class SupplierController extends Controller
{
    protected $supplier_repository;

    public function __construct(ISupplierRepository $supplierRepository)
    {
        $this->supplier_repository = $supplierRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response($this->supplier_repository->list());
    }

    /**
     * @param SupplierStoreRequest $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function store(SupplierStoreRequest $request)
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
        return response($this->supplier_repository->get($id));
    }

    /**
     * @param SupplierUpdateRequest $request
     * @param $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function update($id)
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
