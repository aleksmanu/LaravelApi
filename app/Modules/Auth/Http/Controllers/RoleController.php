<?php

namespace App\Modules\Auth\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Auth\Repositories\Eloquent\RoleRepository;

class RoleController extends Controller
{

    /**
     * @var RoleRepository
     */
    protected $role_repository;

    /**
     * RoleController constructor.
     * @param RoleRepository $repository
     */
    public function __construct(RoleRepository $repository)
    {
        $this->role_repository = $repository;
    }

    /**
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function index()
    {
        return response($this->role_repository->getRoles());
    }

    public function indexInternal()
    {
        return response($this->role_repository->getInternalRoles());
    }

    public function indexExternal()
    {
        return response($this->role_repository->getExternalRoles());
    }
}
