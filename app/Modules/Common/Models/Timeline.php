<?php

namespace App\Modules\Common\Models;

use App\Modules\Auth\Models\User;
use App\Modules\Common\Classes\Abstracts\BaseModel;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

class Timeline extends BaseModel
{
    /*
     * CAREFUL WITH THESE !!
     *
     * Before deleting an index, check whether it's used anywhere first
     * Or this will blow up in your dumb face during runtime
     */
    const TIMELINE_TYPES = [
        'SITE' => [
            'UPDATED' => 'site_updated'
        ],

        'STEP' => [
            'COMPLETED' => 'step_completed'
        ],

        'NOTE' => [
            'CREATED' => 'note_created'
        ],

        'DOCUMENT' => [
            'CREATED' => 'doc_created'
        ]
    ];

    public static function getTableName(): string
    {
        return 'timeline';
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'entity_id',
        'entity_type',
        'user_id',
        'data'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * The attributes that should be cast to Date objects
     *
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $with = [
        'user'
    ];

    public static function fromUserAction($target_ent, string $type, array $extraData = [])
    {
        $user = request()->user();
        $entry = Timeline::create(['user_id' => $user->id]);
        $entry->entity_id = $target_ent->id;
        $entry->entity_type = get_class($target_ent);
        $entry->data = json_encode($target_ent->constructTimelineDataPacket($type, $extraData));
        $entry->save();

        return $entry;
    }

    public function parent()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
