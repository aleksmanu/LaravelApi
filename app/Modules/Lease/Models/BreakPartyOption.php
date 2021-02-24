<?php

namespace App\Modules\Lease\Models;

use App\Modules\Account\Models\Account;
use App\Modules\Client\Models\ClientAccount;
use App\Modules\Client\Models\Portfolio;
use App\Modules\Common\Classes\Abstracts\BaseModel;
use App\Modules\Common\Traits\IsFilteredByClientTrait;
use App\Modules\Property\Models\Property;
use App\Modules\Property\Models\Unit;
use Illuminate\Database\Eloquent\SoftDeletes;

class BreakPartyOption extends BaseModel
{
    use SoftDeletes, IsFilteredByClientTrait;

    /**
     * @return string
     */
    public static function getTableName(): string
    {
        return 'break_party_options';
    }

    public static function getJoinPathToAccount() : array
    {
        return [
            [Lease::getTableName(), self::getTableName() . '.id', 'break_party_option_id'],
            [Unit::getTableName(), Lease::getTableName() . '.unit_id'],
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
    protected $fillable = [
        'name',
        'slug',
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

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function leases()
    {
        return $this->hasMany(Lease::class);
    }
}
