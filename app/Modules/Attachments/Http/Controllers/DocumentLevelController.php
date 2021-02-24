<?php

namespace App\Modules\Attachments\Http\Controllers;

use App\Modules\Attachments\Models\DocumentLevel;

use App\Http\Controllers\Controller;

class DocumentLevelController extends Controller
{
    public function index()
    {
        return response(DocumentLevel::all());
    }
}
