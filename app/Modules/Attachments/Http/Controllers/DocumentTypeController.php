<?php

namespace App\Modules\Attachments\Http\Controllers;

use App\Modules\Attachments\Http\Requests\DocumentTypeIndexRequest;
use App\Modules\Attachments\Http\Requests\DocumentTypePatchRequest;
use App\Modules\Attachments\Http\Requests\DocumentTypeStoreRequest;
use App\Modules\Attachments\Models\DocumentLevel;
use App\Modules\Attachments\Models\DocumentType;
use Illuminate\Support\Arr;

use App\Http\Controllers\Controller;

class DocumentTypeController extends Controller
{
    public function index(DocumentTypeIndexRequest $request)
    {
        $valid = $request->validated();
        $typesQuery = DocumentType::query();

        if (Arr::has($valid, 'document_level_name') && $valid['document_level_name']) {
            $typesQuery->whereHas('documentLevels', function ($query) use ($valid) {
                $query->where(DocumentLevel::getTableName() . '.name', $valid['document_level_name']);
            });
        }

        return response($typesQuery->orderBy('name')->get());
    }

    public function store(DocumentTypeStoreRequest $request)
    {
        $valid = $request->validated();

        $docType = DocumentType::create($valid);
        $docType->documentLevels()->attach($valid['levels']);
        $docType->save();

        return response($docType);
    }

    public function patch(DocumentTypePatchRequest $request, DocumentType $documentType)
    {
        $valid = $request->validated();
        $documentType->update($valid);

        return response($documentType);
    }
}
