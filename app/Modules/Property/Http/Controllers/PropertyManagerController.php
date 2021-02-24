<?php
namespace App\Modules\Property\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\Auth\Models\User;
use App\Modules\Property\Http\Requests\PropertyManagers\PropertyManagerIndexRequest;
use App\Modules\Property\Http\Requests\PropertyManagers\PropertyManagerStoreRequest;
use App\Modules\Property\Http\Requests\PropertyManagers\PropertyManagerUpdateRequest;
use App\Modules\Property\Models\PropertyManager;
use App\Modules\Property\Repositories\PropertyManagerRepository;

class PropertyManagerController extends Controller
{
    /**
     * @var PropertyManagerRepository
     */
    protected $property_manager_repository;

    /**
     * PropertyManagerController constructor.
     * @param PropertyManagerRepository $repository
     */
    public function __construct(PropertyManagerRepository $repository)
    {

        $this->property_manager_repository = $repository;
    }

    public function dataTable(Request $request)
    {
        return response($this->property_manager_repository->getPropertyManagersDataTable(
            $request->sort_column ?? config('misc.dataTable.defaultSortColumn'),
            $request->sort_order ?? config('misc.dataTable.defaultSortOrder'),
            $request->limit ?? config('misc.dataTable.defaultPerPage'),
            intval($request->offset),
            $request->client_id
        ));
    }

    /**
     * @param PropertyManagerIndexRequest $request
     * @return \Illuminate\Http\Response
     */
    public function index(PropertyManagerIndexRequest $request)
    {
        return response($this->property_manager_repository->getPropertyManagers());
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function find(Request $request)
    {
        return response(
            $this->property_manager_repository->findPropertyManagers($request->input('ids'))
        );
    }


    /**
     * @param PropertyManagerStoreRequest $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function store(PropertyManagerStoreRequest $request)
    {
        $validated_data = $request->validated();
        return response($this->property_manager_repository->storePropertyManager($validated_data));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return response($this->property_manager_repository->getPropertyManager($id));
    }

    /**
     * @param PropertyManagerUpdateRequest $request
     * @param $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function update(PropertyManagerUpdateRequest $request, int $id)
    {
        $validated_data = $request->validated();
        return response($this->property_manager_repository->updatePropertyManager($id, $validated_data));
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(int $id)
    {
        return response($this->property_manager_repository->deletePropertyManager($id));
    }

    public function nonManagerUsers()
    {
        return User::whereNotIn('id', function ($query) {
            $query->select('user_id')->from(PropertyManager::getTableName());
        })->get();
    }
}
