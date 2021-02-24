<?php
namespace App\Modules\Lease\Models;

use App\Scopes\FilteredByClientScope;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Modules\Account\Models\Account;
use App\Modules\Attachments\Traits\Attachable;
use App\Modules\Client\Models\ClientAccount;
use App\Modules\Client\Models\Portfolio;
use App\Modules\Common\Classes\Abstracts\BaseModel;
use App\Modules\Common\Traits\IsFilteredByClientTrait;
use App\Modules\Common\Models\Agent;
use App\Modules\Common\Models\Arrears;
use App\Modules\Property\Models\Unit;
use App\Modules\Property\Models\Property;
use App\Modules\Lease\Models\LeaseReview;
use App\Modules\Lease\Models\LeaseType;
use App\Modules\Lease\Traits\HasLeaseBreaksTrait;
use App\Modules\Lease\Traits\HasLeaseChargesTrait;
use App\Modules\Lease\Traits\HasLeaseTransactionsTrait;
use Carbon\Carbon;

class Lease extends BaseModel
{
    // These traits are mostly just used to make this file a little cleaner
    use SoftDeletes;
    use IsFilteredByClientTrait;
    use HasLeaseBreaksTrait;
    use HasLeaseChargesTrait;
    use Attachable;
    use HasLeaseTransactionsTrait;

    public static function getTableName(): string
    {
        return 'leases';
    }

    public static function getJoinPathToAccount(): array
    {
        return [
            [Unit::getTableName(), self::getTableName() . '.unit_id'],
            [Property::getTableName(), Unit::getTableName() . '.property_id'],
            [Portfolio::getTableName(), Property::getTableName() . '.portfolio_id'],
            [ClientAccount::getTableName(), Portfolio::getTableName() . '.client_account_id'],
            [Account::getTableName(), ClientAccount::getTableName() . '.account_id'],
        ];
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $fillable = [
        'unit_id',
        'type_id',
        'payable',

        'cluttons_lease_ref',
        'client_lease_ref',
        'live',
        'stop',
        'stop_by',
        'stop_reason',
        'passing_rent',
        'service_charge',
        'rates_liability',
        'insurance',

        'agreement_date',
        'lease_start',
        'lease_end',
        'next_rent_review',
        'outside_54_act',
        'holding',

        'turnover_rent',
        'review_pattern',
        'first_review',
        'next_review',
        'review',
        'review_notes',
        'review_initiable_by_tenant',
        'time_sensitive',
        'notice_required',
        'upwards_review_only',
        'interest_on_late_review',
        'review_basis',
        'rent_grace',
        'li_bank',
        'li_change_base_interest',

        'managing_agent_id',
        'landlord_id',

        'aga_required',
        'keep_open_clause',
        'assignment',
        'assignment_comments',
        'subletting',
        'subletting_comments',
        'user_clause',
        'user_clause_comments',
        'alterations',
        'repair_obligation',
        'plate_glass_insurance',
        'building_insurance',

        'e_decorations_freq',
        'e_decorations_first',
        'e_decorations_last',
        'i_decorations_freq',
        'i_decorations_first',
        'i_decorations_last',

        'g_organisation',
        'g_security',
        'g_amount',
        'g_expiry_date',
        'g_contact_details',
        'g_notes',

        'lease_notes',
        'lease_status',
        'mgt_remarks'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
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
    ];

    protected $casts = [
        'created_at'  => 'datetime:d/m/Y',
        'updated_at'  => 'datetime:d/m/Y',
        'deleted_at'  => 'datetime:d/m/Y',
        'li_change_base_interest' => 'float',
    ];

    protected $appends = [
        'annual_rent',
        'annual_service_charge',
        'annual_insurance_charge',
        'annual_rate_charge',
        'next_break_date',
        'next_break'
    ];

    /**
     * Eager load attachments
     * @var array
     */
    protected $with = [
        'nextLeaseBreak',
        'leaseBreaks',
        'landlord',
        'managingAgent',
        'leaseReviews',
        'attachments',
        'chargeHistory',
        'arrears',
        'type'
    ];


    protected static function booted()
    {
        static::addGlobalScope(new FilteredByClientScope());
        parent::boot();
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function type()
    {
        return $this->belongsTo(LeaseType::class);
    }

    public function chargeHistory()
    {
        return $this->morphMany(ChargeHistory::class, 'entity');
    }

    public function tenants()
    {
        return $this->hasMany(Tenant::class);
    }

    public function landlord()
    {
        return $this->belongsTo(Agent::class)->where('type', 'landlord');
    }

    public function managingAgent()
    {
        return $this->belongsTo(Agent::class)->where('type', 'managing agent');
    }

    public function leaseReviews()
    {
        return $this->morphMany(LeaseReview::class, 'entity')
            ->where('date', '>=', Carbon::now())
            ->orderBy('date', 'ASC');
    }

    public function arrears()
    {
        return $this->hasMany(Arrears::class);
    }

    public function getPassingRentAttribute()
    {   
        return $this->rentCharges()->sum('annual');
    }
}
