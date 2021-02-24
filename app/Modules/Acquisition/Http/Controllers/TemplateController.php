<?php

namespace App\Modules\Acquisition\Http\Controllers;

use App\Modules\Acquisition\Models\Acquisition;
use App\Modules\Acquisition\Models\Checklist;
use App\Modules\Acquisition\Models\Step;

use App\Modules\Acquisition\Repositories\TemplateRepository;

use Illuminate\Http\Request;
use \Illuminate\Http\Response;

use App\Http\Controllers\Controller;

class TemplateController extends Controller
{
    protected $repository;

    public function __construct(TemplateRepository $templateRepository)
    {
        $this->repository = $templateRepository;
    }

    public function index()
    {
        return response($this->repository->index());
    }

    public function show($template)
    {
        return response($this->repository->get($template));
    }

    public function create(Request $request)
    {
        return response($this->repository->create($request->all()));
    }
}
