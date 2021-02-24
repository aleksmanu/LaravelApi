<?php

namespace App\Modules\Property\Models;

use App\Modules\Account\Models\Account;
use App\Modules\Auth\Models\User;
use App\Modules\Client\Models\ClientAccount;
use App\Modules\Client\Models\Portfolio;
use App\Modules\Common\Classes\Abstracts\BaseModel;
use App\Modules\Common\Traits\IsFilteredByClientTrait;
use App\Modules\Edits\Models\ReviewStatus;
use App\Modules\Edits\Traits\Editable;
use App\Modules\Lease\Models\Lease;
use App\Modules\Lease\Models\Tenant;
use App\Modules\Lease\Models\Transaction;
use App\Scopes\FilteredByClientScope;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Modules\Attachments\Traits\Attachable;
use Illuminate\Support\Facades\Cache;

class Unit extends BaseModel
{
    use SoftDeletes;
    use Editable;
    use Attachable;
    use IsFilteredByClientTrait;

    /**
     * @return string
     */
    public static function getTableName(): string
    {
        return 'units';
    }

    public static function getJoinPathToAccount(): array
    {
        return [
            [Property::getTableName(), self::getTableName() . '.property_id'],
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
        'property_id',
        'property_manager_id',
        'measurement_unit_id',
        'review_status_id',
        'locked_by_user_id',
        'demise',
        'unit',
        'name',
        'yardi_property_unit_ref',
        'yardi_import_ref',
        'yardi_unit_ref',
        'measurement_value',
        'approved_at',
        'approved',
        'approved_initials',
        'held_at',
        'held_initials',
        'locked_at',
        'is_virtual'
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
        'approved_at',
        'held_at',
        'created_at',
        'updated_at',
        'deleted_at',
        'locked_at'
    ];

    protected $casts = [
        'created_at'  => 'datetime:d/m/Y',
        'updated_at'  => 'datetime:d/m/Y',
        'deleted_at'  => 'datetime:d/m/Y',
        'held_at'     => 'datetime:Y-m-d',
        'approved_at' => 'datetime:Y-m-d',
    ];

    /**
     * @var array
     */
    protected $editable = [
        'demise',
        'name',
        'property_manager_id',
        'measurement_unit_id'
    ];

    protected $appends = [
        'rent_per_annum',
        'service_charge_per_annum',
        'insurance_per_annum',
        'rate_per_annum',
    ];

    /**
     * Eager load attachments
     * @var array
     */
    protected $with = [
        'attachments'
    ];

    protected $withCount = [
        'tenants'
    ];


    protected static function booted()
    {
        static::addGlobalScope(new FilteredByClientScope());
        parent::boot();
    }

    public function getRealTenantCountAttribute()
    {
        return Cache::rememberForever("units.{$this->id}.realTenantCount", function () {
            return $this->tenants()->get()->count();
        });
    }

    public function getRentPerAnnumAttribute()
    {
        return Cache::rememberForever("units.{$this->id}.rentPerAnnum", function () {
            $leases = $this->leases()->where('live', '1')->get();

            return $leases->sum('annual_rent');
        });
    }

    public function getServiceChargePerAnnumAttribute()
    {
        return Cache::rememberForever("units.{$this->id}.serviceChargePerAnnum", function () {
            $leases = $this->leases()->where('live', '1')->get();

            return $leases->sum('annual_service_charge');
        });
    }

    public function getInsurancePerAnnumAttribute()
    {
        return Cache::rememberForever("units.{$this->id}.insurancePerAnnum", function () {
            return $this->leases->sum('annual_insurance_charge');
        });
    }

    public function getRatePerAnnumAttribute()
    {
        return Cache::rememberForever("units.{$this->id}.ratesPerAnnum", function () {
            return $this->leases->sum('annual_rate_charge');
        });
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function property()
    {
        return $this->belongsTo(Property::class)->withoutGlobalScope(FilteredByClientScope::class);
    }

    public function propertyBare()
    {
        return $this->belongsTo(Property::class, 'property_id', 'id')->withoutGlobalScope(FilteredByClientScope::class)
            ->without((new Property())->with);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function propertyManager()
    {
        return $this->belongsTo(PropertyManager::class)->withoutGlobalScope('client_filter');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function measurementUnit()
    {
        return $this->belongsTo(MeasurementUnit::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function leases()
    {
        return $this->hasMany(Lease::class)->withoutGlobalScope(FilteredByClientScope::class);
    }

    public function payableLeases()
    {
        return $this->leases()->where('leases.payable', 1);
    }

    public function receivableLeases()
    {
        return $this->leases()->where('leases.payable', 0);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function tenants()
    {
        return $this->hasManyThrough(Tenant::class, Lease::class);
    }

    public function uniqueTenants()
    {
        return $this->tenants()->groupBy('yardi_tenant_ref');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function reviewStatus()
    {
        return $this->belongsTo(ReviewStatus::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function lockedByUser()
    {
        return $this->belongsTo(User::class, 'locked_by_user_id');
    }

    public function transactions()
    {
        return $this->hasManyThrough(Transaction::class, Lease::class);
    }
}
