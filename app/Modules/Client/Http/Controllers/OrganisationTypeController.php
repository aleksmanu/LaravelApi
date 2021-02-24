<?php

namespace App\Modules\Client\Http\Controllers;

use App\Modules\Client\Http\Requests\OrganisationTypes\OrganisationTypeIndexRequest;
use App\Modules\Client\Http\Requests\OrganisationTypes\OrganisationTypeStoreRequest;
use App\Modules\Client\Http\Requests\OrganisationTypes\OrganisationTypeUpdateRequest;
use App\Modules\Client\Models\OrganisationType;
use App\Modules\Client\Repositories\OrganisationTypeRepository;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

class OrganisationTypeController extends Controller
{
    /**
     * @var OrganisationTypeRepository
     */
    protected $org_type_repository;

    /**
     * OrganisationTypeController constructor.
     * @param OrganisationTypeRepository $repository
     */
    public function __construct(OrganisationTypeRepository $repository)
    {
        $this->org_type_repository = $repository;
    }

    /**
     * @param OrganisationTypeIndexRequest $request
     * @return \Illuminate\Http\Response
     */
    public function index(OrganisationTypeIndexRequest $request)
    {
        return response($this->org_type_repository->getOrganisationTypes());
    }

    /**
     * @param OrganisationTypeStoreRequest $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function store(OrganisationTypeStoreRequest $request)
    {

        return response($this->org_type_repository->storeOrganisationType($request->validated()));
    }

    /**
     * @param OrganisationType $organisationType
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function show(OrganisationType $organisationType)
    {
        return response($organisationType);
    }

    /**
     * @param OrganisationTypeUpdateRequest $request
     * @param $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function update(OrganisationTypeUpdateRequest $request, $id)
    {
        return response($this->org_type_repository->updateOrganisationType($id, $request->validated()));
    }

    /**
     * @param OrganisationType $organisationType
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(OrganisationType $organisationType)
    {
        $organisationType->delete();

        return response($organisationType);
    }
}
