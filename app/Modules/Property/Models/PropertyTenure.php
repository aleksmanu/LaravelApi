<?php

namespace App\Modules\Property\Models;

use App\Modules\Account\Models\Account;
use App\Modules\Client\Models\ClientAccount;
use App\Modules\Client\Models\Portfolio;
use App\Modules\Common\Classes\Abstracts\BaseModel;
use App\Modules\Common\Traits\IsFilteredByClientTrait;
use App\Scopes\FilteredByClientScope;
use Illuminate\Database\Eloquent\SoftDeletes;

class PropertyTenure extends BaseModel
{

    use SoftDeletes, IsFilteredByClientTrait;

    /**
     * @return string
     */
    public static function getTableName(): string
    {
        return 'property_tenures';
    }

    public static function getJoinPathToAccount(): array
    {
        return [
            [Property::getTableName(), self::getTableName() . '.id', 'property_tenure_id'],
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
    protected $fillable = [
        'name',
        'slug'
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
        'deleted_at',
    ];

    protected static function booted()
    {
        parent::boot();
        static::addGlobalScope(new FilteredByClientScope());
        static::addGlobalScope('slug', function ($builder) {
            $builder->whereIn('property_tenures.slug', explode(",", env('TENURES', 'freehold,leasehold,licence')));
        });
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function properties()
    {
        return $this->hasMany(Property::class)->withoutGlobalScope(FilteredByClientScope::class);
    }
}
