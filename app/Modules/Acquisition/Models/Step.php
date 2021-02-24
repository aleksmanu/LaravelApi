<?php
namespace App\Modules\Acquisition\Models;

use App\Modules\Attachments\Models\Document;
use App\Modules\Auth\Models\User;
use App\Modules\Auth\Models\Role;

use App\Modules\Common\Classes\Abstracts\BaseModel;
use App\Modules\Common\Contracts\IConfiguresTimeline;
use App\Modules\Common\Models\Note;
use App\Modules\Common\Traits\HasTimelineTrait;
use App\Modules\Common\Traits\HasNotes;
use App\Modules\Attachments\Traits\Attachable;
use App\Modules\Common\Models\Timeline;
use Illuminate\Support\Facades\Cache;

class Step extends BaseModel implements IConfiguresTimeline
{
    use HasNotes, Attachable, HasTimelineTrait;

    public static function getTableName(): string
    {
        return 'acquisition_steps';
    }

    public static function getTimelineDefinitions()
    {
        return [
            Timeline::TIMELINE_TYPES['STEP']['COMPLETED'] => [
                'header' => ''
            ]
        ];
    }

    protected $dates = [
        'completed_on',
        'created_at',
        'deleted_at',
        'target_date',
        'forecast_for',
        'start_on',
    ];

    protected $fillable = [
        "acquisition_checklist_id",
        "acquisition_step_group_id",
        "completed_on",
        "completed_by",
        "client_account_id",
        "depends_on_step_order_number",
        "duration_days",
        "label",
        "mandatory",
        "order",
        "role_id",
        "type",
        "value",
        'target_date',
        'forecast_for',
        'start_on',
    ];

    public $with = [
        "role",
        "attachments",
        "notes",
        "notes.user"
    ];

    protected $appends = [
        'minimal_context'
    ];

    public function getMinimalContextAttribute()
    {
        /**
         * Must be invalidated whenever one of the accessed values is updated
         */
        return Cache::rememberForever("acquisition_steps.{$this->id}.minimalContext", function () {
            if (!$this->bareChecklist || $this->bareChecklist->is_template) {
                return [];
            }

            return [
                "acquisition" => [
                    "id" => $this->bareChecklist->bareSite->barePopArea->acquisition_id,
                    "name" => $this->bareChecklist->bareSite->barePopArea->bareAcquisition->name
                    //Acqui update 'name' handled for invalidation
                ],
                "site" => [
                    "reference" => $this->bareChecklist->bareSite->reference // Site rupdate 'reference' handled
                ],
                "pop_area" => $this->bareChecklist->bareSite->barePopArea // Pop area update * handled
            ];
        });
    }

    public function checklist()
    {
        return $this->belongsTo(Checklist::class, 'acquisition_checklist_id', '');
    }

    public function bareChecklist()
    {
        return $this->checklist()->without((new Checklist())->with);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function stepGroup()
    {
        return $this->belongsTo(StepGroup::class, 'acquisition_step_group_id');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class);
    }

    public function noteAssociatedTimelines()
    {
        return Timeline::where('entity_type', Note::class)
            ->whereIn('entity_id', $this->notes()->pluck('notes.id')->toArray())->get();
    }

    public function attachmentAssociatedTimelines()
    {
        return Timeline::where('entity_type', Document::class)
            ->whereIn('entity_id', $this->attachments()->pluck(Document::getTableName() . '.id')->toArray())->get();
    }

    /*
     * only snowflakes like Step which has relative-attached timeline items need agre
     */
    public function allTimelines()
    {
        return collect($this->timelines)
            ->merge($this->noteAssociatedTimelines())
            ->merge($this->attachmentAssociatedTimelines())
            ->sortByDesc('created_at')
            ->values();
    }

    protected static function boot()
    {
        parent::boot();
        static::updated(function ($step) {
            if (in_array('completed_on', array_keys($step->getDirty()))) {
                Timeline::fromUserAction($step, Timeline::TIMELINE_TYPES['STEP']['COMPLETED']);

                $step->bareChecklist->bareSite->barePopArea->invalidateCache();
            }
        });
    }

    public function constructTimelineDataPacket(string $type, array $extraData): array
    {
        switch ($type) {
            case Timeline::TIMELINE_TYPES['STEP']['COMPLETED']:
                return [
                    'header' => 'COMPLETION DATE MODIFIED',
                    'body' => 'Completion date has been set to',
                    'stamp' => $this->completed_on ? $this->completed_on->toDateTimeString() : null
                ];
        }
    }

    public function invalidateCache()
    {
        Cache::forget("acquisition_steps.{$this->id}.minimalContext");
    }
}
