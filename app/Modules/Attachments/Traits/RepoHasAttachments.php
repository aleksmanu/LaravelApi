<?php

namespace App\Modules\Attachments\Traits;

use App\Modules\Attachments\Models\Document;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Modules\Attachments\Http\Requests\UploadRequest;
use Illuminate\Support\Facades\Storage;
use App;
use App\Modules\Attachments\Http\Requests\UploadWithCatRequest;
use App\Modules\Common\Repositories\NoteRepository;

trait RepoHasAttachments
{
    protected function getDisk(): string
    {
        return App::environment('local') ? 'local' : 's3';
    }

    public function storeAttachment(int $assetId, UploadRequest $fileInfo)
    {
        $data = $fileInfo->validated();
        $file = $data['file'];
        $ref = $data['reference'];
        $extension = $file->getClientOriginalExtension();
        $filename = isset($data['filename']) ? "{$data['filename']}.$extension" : $file->getClientOriginalName();
        $mimes = $file->getMimeType();
        $content = $file->get();

        $attachment = $this->model::find($assetId)
            ->attachments()
            ->create([
                'filename' => $filename,
                'mime_type' => $mimes,
                'reference' => $ref ?? '',
                'user_id' => Auth::user()['id'],
                'document_type_id' => is_numeric($data['document_type_id']) ? $data['document_type_id'] : null,
                'parties' => $data['parties'] ?? '',
                'comments' => $data['comments'] ?? '',
                'date' => $data['date'] ?? date('Y-m-d')
            ]);

        Storage::disk($this->getDisk())->putFileAs('attachments', $file, $attachment->id);
        $attachment->load(['documentType']);
        return $attachment;
    }

    public function storeAttachmentWithCat(int $assetId, UploadWithCatRequest $fileInfo)
    {
        $data = $fileInfo->validated();
        $file = $data['file'];
        $ref = $data['reference'];
        $extension = $file->getClientOriginalExtension();
        $filename = isset($data['filename']) ? "{$data['filename']}.$extension" : $file->getClientOriginalName();
        $mimes = $file->getMimeType();
        $content = $file->get();

        $attachment = $this->model::find($assetId)
            ->attachments()
            ->create([
                'filename' => $filename,
                'mime_type' => $mimes,
                'reference' => $ref ?? '',
                'user_id' => Auth::user()['id'],
                'document_type_id' => $data['document_type_id'],
                'parties' => $data['parties'] ?? '',
                'comments' => $data['comments'] ?? '',
                'date' => $data['date'] ?? Carbon::now()
            ]);

        Storage::disk($this->getDisk())->putFileAs('attachments', $file, $attachment->id);
        $attachment->load(['documentType']);
        return $attachment;
    }

    public function indexAttachments(int $assetId)
    {
        return $this->model::find($assetId)
            ->attachments()
            ->orderBy('created_at')
            ->get();
    }

    public function indexPhotos(int $assetId)
    {
        return $this->model::find($assetId)
            ->attachments()
            ->where('document_type_id', 1)
            ->orderBy('created_at')
            ->get();
    }

    public function deleteAttachment(string $attachmentId): int
    {
        return Document::destroy($attachmentId);
    }

    public function showAttachment(string $attachmentId): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $attachment = Document::where('id', $attachmentId)
            ->firstOrFail();

        return Storage::disk($this->getDisk())->response(
            'attachments/' . $attachment->id,
            $attachment->filename,
            [
                'Content-Type' => $attachment->mime_type,
            ]
        );
    }

    public function archiveAttachment(string $attachmentId, bool $newState)
    {
        $doc = Document::where('id', $attachmentId)
            ->firstOrFail();

        $doc->archived_at = $newState ? Carbon::now() : null;
        $doc->save();

        $noteRepo = app()->make(NoteRepository::class);
        $newNote = $noteRepo->store([
            'user_id' => auth()->user()->id,
            'note' => '[AUTOMATED SUBMISSION] I have ' . ($newState ? 'ARCHIVED' : 'UNARCHIVED') . ' document ' .
                $doc->filename . ' (' . $doc->reference . ').',
            'is_internal' => true
        ]);

        return $doc;
    }
}
