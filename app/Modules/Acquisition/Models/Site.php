<?php
namespace App\Modules\Acquisition\Models;

use App\Modules\Attachments\Models\Document;
use App\Modules\Attachments\Traits\Attachable;
use App\Modules\Common\Contracts\IConfiguresTimeline;
use App\Modules\Common\Models\Note;
use App\Modules\Common\Models\Timeline;
use App\Modules\Common\Traits\HasNotes;
use App\Modules\Common\Traits\HasTimelineTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Site extends Model implements IConfiguresTimeline
{
    use HasTimelineTrait;
    use HasNotes;
    use Attachable;

    protected $table = 'acquisition_sites';

    /**
     * The attributes that should be cast to Date objects
     *
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        "reference",
        "unit",
        "status",
        "number",
        "building",
        "street",
        "estate",
        "suburb",
        "town",
        "county",
        "country",
        "postcode",
        "surveyor_name",
        "agent_mobile",
        "agent_email",
        "landlord_address",
        "landlord_name",
        "landlord_telephone",
        "landlord_email",
        "landlord_agent_address",
        "landlord_agent_name",
        "landlord_agent_telephone",
        "landlord_agent_email",
        "landlord_solicitor_address",
        "landlord_solicitor_name",
        "landlord_solicitor_telephone",
        "landlord_solicitor_email",
        "latitude",
        "longitude",
        "planning_type",
        "planning_application_number",
        "pop_area_id",
        "client_ref",
        "council_contact_name",
        "council_tel",
        "council_email",
        "council_address",
        "network_planner",
    ];

    public static $siteInformationFieldList = [
        'reference',
        'postcode',
        'surveyor_name',
        'agent_mobile',
        'agent_email',
        'unit',
        'number',
        'building',
        'street',
        'estate',
        'suburb',
        'town',
        'county',
        'country',
        'landlord_address',
        'landlord_name',
        'landlord_telephone',
        'landlord_email',
        'landlord_agent_address',
        'landlord_agent_name',
        'landlord_agent_telephone',
        'landlord_agent_email',
        'landlord_solicitor_address',
        'landlord_solicitor_name',
        'landlord_solicitor_telephone',
        'landlord_solicitor_email'
    ];

    public static $propertyDealInformationFieldList = [
        'action',
        'status',
        'option',
        'site_type',
        'proposed_rent',
        'purchase_price',
        'agreed_rent',
        'rent_free',
        'rent_free_price',
        'flood_risk',
        'planning_type',
        'planning_application_number',
    ];

    protected $withCount = [
    ];

    public $with = [
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float'
    ];

    public function popArea()
    {
        return $this->belongsTo(PopArea::class);
    }

    public function barePopArea()
    {
        return $this->popArea()->without((new PopArea())->with);
    }

    public function checklist()
    {
        return $this->hasOne(Checklist::class, 'acquisition_site_id');
    }

    public function steps()
    {
        return $this->hasManyThrough(Step::class, Checklist::class, 'acquisition_site_id', 'acquisition_checklist_id');
    }

    public function bareSteps()
    {
        return $this->steps()->without((new Step())->with);
    }

    public function overdueSteps()
    {
        return $this->bareSteps()->whereNull('completed_on')->whereDate('forecast_for', '<', Carbon::now());
    }

    public function stepAssociatedTimelines()
    {
        $entries = collect([]);

        foreach ($this->steps as $step) {
            $newEntries = $step->allTimelines();

            foreach ($newEntries as $entry) {
                $data = json_decode($entry->data);
                $data->scope = $step->label;
                $data->scope_group = $step->stepGroup->name;
                $entry->data = json_encode($data);
            }

            $entries = $entries->merge($newEntries);
        }

        return $entries->sortByDesc('created_at')->values();
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

    public function allTimelines()
    {
        return $this->stepAssociatedTimelines()
            ->merge($this->attachmentAssociatedTimelines())
            ->merge($this->timelines)
            ->merge($this->noteAssociatedTimelines())
            ->sortByDesc('created_at')
            ->values();
    }

    public function stepAssociatedDocuments()
    {
        return Document::where('attachable_type', Step::class)
            ->whereIn('attachable_id', $this->steps()->pluck(Step::getTableName() . '.id')->toArray())->get();
    }

    public function documentsIncludingFromSteps()
    {
        return $this->attachments
            ->merge($this->stepAssociatedDocuments())
            ->sortBy('created_at')
            ->values();
    }

    public function stepAssociatedNotes()
    {
        return Note::where('entity_type', Step::class)
            ->whereIn('entity_id', $this->steps()->pluck(Step::getTableName() . '.id')->toArray())->get();
    }

    public function notesIncludingFromSteps()
    {
        $stepNotes = $this->stepAssociatedNotes();

        foreach ($stepNotes as $key => $note) {
            $note['note'] = '[' . $note->entity->label . '] ' . $note['note'];
            $stepNotes[$key] = $note;
        }

        return $this->notes
            ->merge($stepNotes)
            ->sortByDesc('created_at')
            ->values();
    }

    protected static function boot()
    {
        parent::boot();
        static::updated(function ($obj) {
            if (array_intersect(self::$siteInformationFieldList, array_keys($obj->getDirty()))
                || array_intersect(self::$propertyDealInformationFieldList, array_keys($obj->getDirty()))) {
                Timeline::fromUserAction($obj, Timeline::TIMELINE_TYPES['SITE']['UPDATED'], $obj->getDirty());
            }

            if (in_array('reference', array_keys($obj->getDirty()))) {
                foreach ($obj->steps as $obj) {
                    $obj->invalidateCache();
                }
            }
        });
    }

    public function constructTimelineDataPacket(string $type, array $extraData): array
    {
        switch ($type) {
            case Timeline::TIMELINE_TYPES['SITE']['UPDATED']:
                $updatedTab = array_intersect(
                    self::$siteInformationFieldList,
                    array_keys($extraData)
                ) ? 'SITE INFORMATION' : (array_intersect(
                    self::$propertyDealInformationFieldList,
                    array_keys($extraData)
                ) ? 'PROPERTY/DEAL INFORMATION' : 'MISC PROPERTIES');
                unset($extraData['updated_at']);
                return [
                    'header' => $updatedTab . ' MODIFIED',
                    'body' => 'The following information has been set',
                    'changes' => $extraData
                ];
                break;
            default:
                throw new \Exception(get_class($this) . ' tried to render an invalid timeline packet (see Model file)');
        }
    }
}
