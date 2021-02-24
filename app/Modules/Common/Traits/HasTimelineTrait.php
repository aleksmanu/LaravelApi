<?php


namespace App\Modules\Common\Traits;

use App\Modules\Common\Models\Timeline;

trait HasTimelineTrait
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function timelines()
    {
        return $this->morphMany(Timeline::class, 'entity')->orderBy('created_at', 'desc');
    }
}
