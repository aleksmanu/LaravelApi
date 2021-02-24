<?php

namespace App\Modules\Property\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Property\Repositories\PartnerRepository;

class PartnerController extends Controller
{
    protected $repository;

    public function __construct(PartnerRepository $PartnerRepository)
    {
        $this->repository = $PartnerRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response($this->repository->getPartners());
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        return response($this->repository->getPartner($id));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexWithProps()
    {
        return response($this->repository->getPropertiesForPartners());
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showWithProps(int $id)
    {
        return response($this->repository->getPropertiesForPartner($id));
    }
}
