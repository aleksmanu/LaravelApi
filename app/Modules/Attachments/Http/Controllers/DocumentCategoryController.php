<?php

namespace App\Modules\Attachments\Http\Controllers;

use App\Modules\Attachments\Models\DocumentCategory;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

class DocumentCategoryController extends Controller
{
    public function index()
    {
        return response(
            DocumentCategory::query()
                ->where(DocumentCategory::getTableName() . '.id', '!=', 1)
                ->get()
        );
    }
}
