<?php

namespace App\Modules\Common\Models;

use App\Modules\Acquisition\Models\Site;
use App\Modules\Acquisition\Models\Step;
use App\Modules\Auth\Models\User;
use App\Modules\Common\Contracts\IConfiguresTimeline;
use App\Modules\Common\Traits\HasTimelineTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Note extends Model implements IConfiguresTimeline
{
    use HasTimelineTrait;

    /**
     * @var array
     */
    protected $fillable = [
        'user_id',
        'entity_type',
        'entity_id',
        'note',
        'is_internal',
        'divider'
    ];

    /**
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at'
    ];

    protected $with = [
        'user'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function entity()
    {
        return $this->morphTo();
    }

    protected static function booted()
    {
        parent::boot();

        static::addGlobalScope('internal', function (Builder $builder) {
            if (auth()->check() && !auth()->user()->isSystemUser()) {
                $builder->where('is_internal', false);
            }
        });

        static::created(function ($note) {
            if ($note->entity_type === Step::class) {
                Timeline::fromUserAction($note, Timeline::TIMELINE_TYPES['NOTE']['CREATED']);
            } elseif ($note->entity_type === Site::class) {
                Timeline::fromUserAction($note, Timeline::TIMELINE_TYPES['NOTE']['CREATED']);
            }
        });
    }

    public function constructTimelineDataPacket(string $type, array $extraData): array
    {
        switch ($type) {
            case Timeline::TIMELINE_TYPES['NOTE']['CREATED']:
                return [
                    'header' => 'NOTE ADDED',
                    'body' => 'Note contents are',
                    'note' => $this->note,
                    'scope_group' => $this->divider
                ];
            default:
                return [];
        }
    }
}
