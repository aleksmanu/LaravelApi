<?php
namespace App\Modules\Common\Traits;

use App\Modules\Common\Models\Note;

trait HasNotes
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function notes()
    {
        return $this->morphMany(Note::class, 'entity')->orderBy('created_at', 'desc');
    }
}
