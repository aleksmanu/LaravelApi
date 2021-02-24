<?php

namespace App\Modules\Attachments\Http\Controllers;

use App\Modules\Attachments\Http\Requests\DocumentPatchRequest;
use App\Modules\Attachments\Models\Document;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

class DocumentController extends Controller
{
    public function patch(DocumentPatchRequest $request, Document $document)
    {
        $valid = $request->validated();

        $document->update($valid);
        $document->save();

        return response($document);
    }
}
