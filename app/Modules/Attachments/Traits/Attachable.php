<?php

namespace App\Modules\Attachments\Traits;

use App\Modules\Attachments\Models\Document;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait Attachable
{
    /**
     * @return mixed
     */
    public function getAttachments()
    {
        return $this->getAttribute('attachments');
    }

    /**
     * @return MorphMany
     */
    public function attachments(): MorphMany
    {
        return $this->morphMany(Document::class, 'attachable');
    }

    public function documents(): MorphMany
    {
        return $this->attachments();
    }
}
