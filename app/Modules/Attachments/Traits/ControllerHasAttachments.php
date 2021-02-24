<?php
namespace App\Modules\Attachments\Traits;

use App\Modules\Attachments\Http\Requests\UploadRequest;
use App\Modules\Attachments\Http\Requests\UploadWithCatRequest;

trait ControllerHasAttachments
{
    public function storeAttachment(int $assetId, UploadRequest $request)
    {
        return $this->attachable_repository->storeAttachment($assetId, $request);
    }

    public function storeAttachmentWithCat(int $assetId, UploadWithCatRequest $request)
    {
        return $this->attachable_repository->storeAttachmentWithCat($assetId, $request);
    }

    public function indexAttachments(int $assetId)
    {
        return $this->attachable_repository->indexAttachments($assetId);
    }

    public function indexPhotos(int $assetId)
    {
        return $this->attachable_repository->indexPhotos($assetId);
    }

    public function deleteAttachment(string $attachmentId)
    {
        return $this->attachable_repository->deleteAttachment($attachmentId);
    }

    public function showAttachment(string $attachmentId)
    {
        return $this->attachable_repository->showAttachment($attachmentId);
    }

    public function archiveAttachment(string $attachmentId)
    {
        return $this->attachable_repository->archiveAttachment($attachmentId, true);
    }

    public function unArchiveAttachment(string $attachmentId)
    {
        return $this->attachable_repository->archiveAttachment($attachmentId, false);
    }
}
