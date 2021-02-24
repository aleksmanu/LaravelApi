<?php
namespace App\Modules\Edits\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Edits\Repositories\EditStatusRepository;

class EditStatusController extends Controller
{

    /**
     * @var EditStatusRepository
     */
    private $repository;

    /**
     * EditStatusController constructor.
     * @param EditStatusRepository $repository
     */
    public function __construct(EditStatusRepository $repository)
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
