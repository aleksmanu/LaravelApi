<?php

namespace App\Modules\Edits\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Edits\Repositories\EditBatchTypeRepository;

class EditBatchTypeController extends Controller
{
    /**
     * @var EditBatchTypeRepository
     */
    private $repository;

    /**
     * EditBatchTypeController constructor.
     * @param EditBatchTypeRepository $repository
     */
    public function __construct(EditBatchTypeRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function index()
    {
        return response($this->repository->findAll());
    }
}
