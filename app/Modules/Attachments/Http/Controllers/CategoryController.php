<?php

namespace App\Modules\Attachments\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Attachments\Models\DocumentCategory;

class CategoryController extends Controller
{
    /**
     * @var DocumentCategory
     */
    protected $model;

    /**
     * CategoryController constructor.
     * @param DocumentCategory $category
     */
    public function __construct(DocumentCategory $category)
    {
        $this->model = $category;
    }

    /**
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function index()
    {
        return response($this->model::all());
    }
}
