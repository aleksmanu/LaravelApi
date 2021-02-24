<?php

namespace App\Modules\Client\Http\Controllers;

use App\Modules\Client\Http\Requests\ClientAccountStatuses\ClientAccountStatusIndexRequest;
use App\Modules\Client\Http\Requests\ClientAccountStatuses\ClientAccountStatusStoreRequest;
use App\Modules\Client\Http\Requests\ClientAccountStatuses\ClientAccountStatusUpdateRequest;
use App\Modules\Client\Repositories\ClientAccountStatusRepository;

use App\Http\Controllers\Controller;
use App\Modules\Core\Library\StringHelper;

class ClientAccountStatusController extends Controller
{
    /**
     * @var ClientAccountStatusRepository
     */
    protected $client_status_repository;

    /**
     * ClientAccountStatusController constructor.
     * @param ClientAccountStatusRepository $repository
     */
    public function __construct(ClientAccountStatusRepository $repository)
    {
        $this->client_status_repository = $repository;
    }

    /**
     * @param ClientAccountStatusIndexRequest $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function index(ClientAccountStatusIndexRequest $request)
    {
        return response($this->client_status_repository->getClientAccountStatuses());
    }

    /**
     * @param ClientAccountStatusStoreRequest $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function store(ClientAccountStatusStoreRequest $request)
    {
        $validated_data = $request->validated();
        $validated_data['slug'] = StringHelper::slugify($validated_data['name']);
        return response($this->client_status_repository->storeClientAccountStatus($validated_data));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        return response($this->client_status_repository->getClientAccountStatus($id));
    }

    /**
     * @param ClientAccountStatusUpdateRequest $request
     * @param $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function update(ClientAccountStatusUpdateRequest $request, $id)
    {
        $validated_data = $request->validated();
        $validated_data['slug'] = StringHelper::slugify($validated_data['name']);
        return response($this->client_status_repository->updateClientAccountStatus($id, $validated_data));
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy($id)
    {
        return response($this->client_status_repository->deleteClientAccountStatus($id));
    }
}
