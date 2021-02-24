<?php

namespace App\Modules\Client\Models;

use App\Modules\Auth\Models\User;
use App\Modules\Account\Models\Account;
use App\Modules\Common\Classes\Abstracts\BaseModel;
use App\Modules\Common\Traits\IsFilteredByClientTrait;
use App\Modules\Edits\Models\ReviewStatus;
use App\Modules\Edits\Traits\Editable;
use App\Modules\Property\Models\Property;
use App\Modules\Property\Models\Unit;
use App\Scopes\FilteredByClientScope;
use Illuminate\Database\Eloquent\SoftDeletes;

class Portfolio extends BaseModel
{

    use SoftDeletes, Editable, IsFilteredByClientTrait;

    /**
     * @return string
     */
    public static function getTableName(): string
    {
        return 'portfolios';
    }

    public static function getJoinPathToAccount() : array
    {
        return [
            [ClientAccount::getTableName(), self::getTableName() . '.client_account_id'],
            [Account::getTableName(), ClientAccount::getTableName() . '.account_id'],
        ];
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $fillable = [
        'client_account_id',
        'review_status_id',
        'locked_by_user_id',
        'name',
        'yardi_portfolio_ref',
        'locked_at'
    ];

    /**
     * @var array
     */
    protected $editable = [
        'name',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [

    ];

    public $with = [
        'clientAccount:id,name,yardi_client_ref'
    ];

    public $withCount = [
        'properties',
        'units',
    ];

    protected $appends = [
        'real_units_count',
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

    protected static function booted()
    {
        static::addGlobalScope(new FilteredByClientScope());
        parent::boot();
    }

    /**
     * @return array
     */
    public function getEditable(): array
    {
        return $this->editable;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function clientAccount()
    {
        return $this->belongsTo(ClientAccount::class)->withoutGlobalScope(FilteredByClientScope::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function properties()
    {
        return $this->hasMany(Property::class)
            ->withoutGlobalScope(FilteredByClientScope::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function units()
    {
        return $this->hasManyThrough(Unit::class, Property::class)
            ->withoutGlobalScope(FilteredByClientScope::class);
    }


    public function getPropertiesCountAttribute()
    {
        return $this->properties()->where('properties.live', true)->count();
    }


    public function getUnitsCountAttribute()
    {
        return $this->units()->count();
    }


    public function getRealUnitsCountAttribute()
    {
        return $this->units()->where('units.is_virtual', false)->count();
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
