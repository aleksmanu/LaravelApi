<?php
namespace App\Modules\Client\Models;

use App\Modules\Account\Models\Account;
use App\Modules\Auth\Models\User;
use App\Modules\Common\Classes\Abstracts\BaseModel;
use App\Modules\Common\Traits\IsFilteredByClientTrait;
use App\Modules\Common\Models\Address;
use App\Modules\Edits\Models\ReviewStatus;
use App\Modules\Edits\Traits\Editable;
use App\Modules\Lease\Models\Lease;
use App\Modules\Lease\Models\Tenant;
use App\Modules\Property\Models\Property;
use App\Modules\Property\Models\PropertyManager;
use App\Modules\Property\Models\Unit;
use App\Scopes\FilteredByClientScope;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Modules\Attachments\Traits\Attachable;

class ClientAccount extends BaseModel
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
        return 'client_accounts';
    }

    public static function getJoinPathToAccount() : array
    {
        return [
            [Account::getTableName(), self::getTableName() . '.account_id'],
        ];
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $fillable = [
        'account_id',
        'organisation_type_id',
        'address_id',
        'property_manager_id',
        'client_account_status_id',
        'review_status_id',
        'locked_by_user_id',
        'name',
        'yardi_client_ref',
        'yardi_alt_ref',
        'locked_at',
    ];

    /**
     * @var array
     */
    protected $editable = [
        'organisation_type_id',
        'client_account_status_id',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [

    ];

    /**
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime:D, M d, Y',
        'updated_at' => 'datetime:D, M d, Y',
        'deleted_at' => 'datetime:D, M d, Y',
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
        'locked_at'
    ];

    /**
     * eager load attachments
     * @var array
     */
    protected $with = [
        'attachments'
    ];

    public $withCount = [
        'portfolios',
        'properties'
    ];

    public $appends = [
        //'tenants_count' way too expensive on large sets, use where needed sensibly
        'units_count',
        'real_units_count',
    ];

    protected static function booted()
    {
        static::addGlobalScope(new FilteredByClientScope());
        parent::boot();
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
    public function clientAccountStatus()
    {
        return $this->belongsTo(ClientAccountStatus::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function account()
    {
        return $this->belongsTo(Account::class)->withoutGlobalScope(FilteredByClientScope::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function organisationType()
    {
        return $this->belongsTo(OrganisationType::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function address()
    {
        return $this->belongsTo(Address::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function portfolios()
    {
        return $this->hasMany(Portfolio::class)->withoutGlobalScope(FilteredByClientScope::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function properties()
    {
        return $this->hasManyThrough(
            Property::class,
            Portfolio::class
        )->withoutGlobalScope(FilteredByClientScope::class);
    }

    /**
     * The following 'relations' have been mimicked, they have some quirks but mostly work fine
     * They don't work in eager loading, or count loading, of course
     */
    public function getUnitsAttribute()
    {
        return Unit::hydrate($this->units()->get()->toArray());
    }

    public function getUnitsCountAttribute()
    {
        return $this->units()->count();
    }

    public function getRealUnitsCountAttribute()
    {
        return $this->realUnits()->count();
    }

    public function units()
    {
        return $this->properties()
            ->withoutGlobalScope(FilteredByClientScope::class)
            ->without((new Property())->with)
            ->leftJoin(Unit::getTableName(), 'units.property_id', '=', 'properties.id')
            ->select(Unit::getTableName() . '.*');
    }

    public function realUnits()
    {
        return $this->units()
            ->where('units.is_virtual', 0);
    }

    public function getLeasesAttribute()
    {
        return Lease::hydrate($this->leases()->get()->toArray());
    }

    public function leases()
    {
        return $this->units()
            ->withoutGlobalScope(FilteredByClientScope::class)
            ->leftJoin(Lease::getTableName(), 'leases.unit_id', '=', 'units.id')
            ->select(Lease::getTableName() . '.*');
    }

    public function getTenantsAttribute()
    {
        return Tenant::hydrate($this->tenants()->get()->toArray());
    }

    public function tenants()
    {
        return $this->leases()
            ->withoutGlobalScope(FilteredByClientScope::class)
            ->rightJoin(Tenant::getTableName(), 'tenants.lease_id', '=', 'leases.id')
            ->select(Tenant::getTableName() . '.*');
    }

    public function getTenantsCountAttribute()
    {
        return $this->tenants()->count();
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
