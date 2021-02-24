<?php

namespace App\Modules\Property\Models;

use App\Modules\Auth\Models\User;
use App\Modules\Account\Models\Account;
use App\Modules\Client\Models\Portfolio;
use App\Modules\Client\Models\ClientAccount;
use App\Modules\Common\Classes\Abstracts\BaseModel;
use App\Modules\Common\Traits\HasNotes;
use App\Modules\Common\Traits\IsFilteredByClientTrait;
use App\Modules\Common\Models\Address;
use App\Modules\Edits\Models\ReviewStatus;
use App\Modules\Edits\Traits\Editable;
use App\Modules\Lease\Models\Lease;
use App\Scopes\FilteredByClientScope;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Modules\Attachments\Traits\Attachable;
use Illuminate\Support\Facades\Cache;

class Property extends BaseModel
{
    use SoftDeletes;
    use Editable;
    use Attachable;
    use IsFilteredByClientTrait;
    use HasNotes;

    /**
     * @return string
     */
    public static function getTableName(): string
    {
        return 'properties';
    }

    public static function getJoinPathToAccount(): array
    {
        return [
            [Portfolio::getTableName(), self::getTableName() . '.portfolio_id'],
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
        'portfolio_id',
        'property_manager_id',
        'address_id',
        'partner_id',
        'property_status_id',
        'property_use_id',
        'property_tenure_id',
        'location_type_id',
        'property_category_id',
        'stop_posting_id',
        'review_status_id',
        'locked_by_user_id',
        'name',
        'yardi_property_ref',
        'yardi_alt_ref',
        'total_lettable_area',
        'void_total_lettable_area',
        'total_site_area',
        'total_gross_internal_area',
        'total_rateable_value',
        'void_total_rateable_value',
        'listed_building',
        'live',
        'conservation_area',
        'air_conditioned',
        'vat_registered',
        'approved',
        'approved_initials',
        'approved_at',
        'held_at',
        'held_initials',
        'locked_at'
    ];

    /**
     * Only these fields are allowed to be editable using the "edits" feature
     * @var array
     */
    protected $editable = [
        'property_manager_id',
        'location_type_id',
        'property_use_id',
        'property_category_id',
        'property_status_id',
        'name',
        'total_lettable_area',
        'void_total_lettable_area',
        'total_site_area',
        'total_gross_internal_area',
        'listed_building',
        'live',
        'conservation_area',
        'air_conditioned',
        'vat_registered',
    ];

    protected static function booted()
    {
        static::addGlobalScope(new FilteredByClientScope());
        parent::boot();
    }

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
        'held_at',
        'approved_at',
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

    public $with = [
        'address',
        'attachments',
        'locationType',
        'partner',
        'propertyCategory',
        'propertyManager',
        'propertyStatus',
        'propertyTenure',
        'propertyUse',
    ];

    public $withCount = [
        'units',
        'leases'
    ];

    // public function getRealTenantCountAttribute()
    // {
    //     return Cache::rememberForever("properties.{$this->id}.realTenantCount", function () {
    //         return $this->units()->where('is_virtual', false)
    //             ->get()->sum('realTenantCount');
    //     });
    // }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function portfolio()
    {
        return $this->belongsTo(Portfolio::class)->withoutGlobalScope(FilteredByClientScope::class);
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
    public function address()
    {
        return $this->belongsTo(Address::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function propertyStatus()
    {
        return $this->belongsTo(PropertyStatus::class)->withoutGlobalScope(FilteredByClientScope::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function propertyUse()
    {
        return $this->belongsTo(PropertyUse::class)->withoutGlobalScope(FilteredByClientScope::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function propertyTenure()
    {
        return $this->belongsTo(PropertyTenure::class)->withoutGlobalScope(FilteredByClientScope::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function locationType()
    {
        return $this->belongsTo(LocationType::class)->withoutGlobalScope(FilteredByClientScope::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function units()
    {
        return $this->hasMany(Unit::class)->withoutGlobalScope(FilteredByClientScope::class);
    }

    public function lettableUnits()
    {
        return $this->units()->where('is_virtual', false);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function partner()
    {
        return $this->belongsTo(Partner::class)->withoutGlobalScope(FilteredByClientScope::class);
    }

    public function leases()
    {
        return $this->hasManyThrough(
            Lease::class,
            Unit::class
        )->withoutGlobalScope(FilteredByClientScope::class);
    }

    public function payableLeases()
    {
        return $this->leases()->where('leases.payable', 1)->where('live', '1');
    }

    public function receivableLeases()
    {
        return $this->leases()->where('leases.payable', 0)->where('live', '1');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function propertyCategory()
    {
        return $this->belongsTo(PropertyCategory::class)->withoutGlobalScope(FilteredByClientScope::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function stopPosting()
    {
        return $this->belongsTo(StopPosting::class)->withoutGlobalScope(FilteredByClientScope::class);
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
}
