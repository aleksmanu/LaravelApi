<?php
namespace App\Modules\Attachments\Models;

use App\Modules\Acquisition\Models\Step;
use App\Modules\Auth\Models\User;
use App\Modules\Common\Classes\Abstracts\BaseModel;
use App\Modules\Common\Contracts\IConfiguresTimeline;
use App\Modules\Common\Models\Timeline;
use App\Modules\Common\Traits\HasTimelineTrait;
use Carbon\Carbon;
use App\Modules\Acquisition\Models\Site;

class Document extends BaseModel implements IConfiguresTimeline
{
    use HasTimelineTrait;

    public static function getTableName(): string
    {
        return 'documents';
    }

    /**
     * Eager load category relation
     * @var array
     */

    public $with = [
        'user', 'documentType'
    ];

    //public $incrementing = false;
    /**
     * Mutable fields
     * @var array
     */
    protected $editable = [
        'filename',
        'reference',
        'mime_type',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'document_type_id',
        'filename',
        'user_id',
        'mime_type',
        'reference',
        'attachable_type',
        'attachable_id',
        'uri',
        'parties',
        'comments',
        'date',
        'archived_at'
    ];

    /**
     * The attributes that should be cast to Date objects
     *
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
        'archived_at'
    ];

    public function attachable()
    {
        return $this->morphTo();
    }

    public function documentType()
    {
        return $this->belongsTo(DocumentType::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($upload) {});
        static::created(function ($upload) {
            if ($upload->attachable_type === Step::class) {
                Timeline::fromUserAction($upload, Timeline::TIMELINE_TYPES['DOCUMENT']['CREATED']);
            }

            if ($upload->attachable_type === Site::class) {
                Timeline::fromUserAction($upload, Timeline::TIMELINE_TYPES['DOCUMENT']['CREATED']);
            }
        });
    }

    public function constructTimelineDataPacket(string $type, array $extraData): array
    {
        switch ($type) {
            case Timeline::TIMELINE_TYPES['DOCUMENT']['CREATED']:
                return [
                    'header'      => 'DOCUMENT ADDED: ' . $this->reference,
                    'body'        => "A document of type '$this->reference'with the name '$this->filename' has been
                        attached.",
                    'scope_group' => $this->documentType->name
                ];
                break;
            default:
                return [];
        }
    }
}
