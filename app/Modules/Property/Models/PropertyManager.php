<?php

namespace App\Modules\Property\Models;

use App\Modules\Account\Repositories\AccountRepository;
use App\Modules\Auth\Models\User;
use App\Modules\Client\Models\ClientAccount;
use App\Modules\Common\Classes\Abstracts\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use App\Modules\Property\Models\Property;
use App\Modules\Property\Models\Unit;
use App\Modules\Property\Repositories\PropertyRepository;
use App\Modules\Property\Repositories\UnitRepository;

class PropertyManager extends BaseModel
{

    use SoftDeletes;

    /**
     * @return string
     */
    public static function getTableName(): string
    {
        return 'property_managers';
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'code',
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

    protected $with = [
        'user',
        'units',
        'realUnits',
    ];

    public $withCount = [
        'properties',
        'units',
        'realUnits',
        'clientAccounts'
    ];

    protected static function booted()
    {
        static::addGlobalScope('client_filter', function (Builder $builder) {
            if (!auth()->check() || auth()->payload()->get('restriction_account_id') == 0) {
                return;
            }

            /*
              * There are two queries running here, UNIONed at the end
              *     First query selects all managers attached to properties or units under Account
              *     Second query selects all managers directly attached to account
              */

            $propFreshBoi = (clone $builder)->withoutGlobalScope('client_filter');
            $unitFreshBoi = (clone $builder)->withoutGlobalScope('client_filter');

            $propRepo = \App::make(PropertyRepository::class);
            $unitRepo = \App::make(UnitRepository::class);
            $accountRepo = \App::make(AccountRepository::class);

            // Prop on Pmanager
            $propFreshBoi->join(
                Property::getTableName() . ' AS clientFilter_' . Property::getTableName(),
                function ($join) use ($propRepo) {
                    $join->on(
                        'clientFilter_' . Property::getTableName() . '.property_manager_id',
                        '=',
                        self::getTableName() . '.id'
                    )->whereIn(
                        'clientFilter_' . Property::getTableName() . '.id',
                        $propRepo->getProperties()->pluck('id')
                    );
                }
            )->groupBy(self::getTableName() . '.id');

            // Unit on Pmanager
            $unitFreshBoi->join(
                Unit::getTableName() . ' AS clientFilter_' . Unit::getTableName(),
                function ($join) use ($unitRepo) {
                    $join->on(
                        'clientFilter_' . Unit::getTableName() . '.property_manager_id',
                        '=',
                        self::getTableName() . '.id'
                    )->whereIn('clientFilter_' . Unit::getTableName() . '.id', $unitRepo->getUnits()->pluck('id'));
                }
            )->groupBy(self::getTableName() . '.id');

            // User on Pmanager
            $builder->join(
                User::getTableName() . ' AS clientFilter_' . User::getTableName(),
                function ($join) use ($accountRepo) {
                    $join->on(
                        'clientFilter_' . User::getTableName() . '.id',
                        '=',
                        self::getTableName() . '.user_id'
                    )->whereIn(
                        'clientFilter_' . User::getTableName() . '.id',
                        $accountRepo->getAccount(auth()->payload()->get('restriction_account_id'))->users->pluck('id')
                    );
                }
            )->groupBy(self::getTableName() . '.id');

            $builder->union($propFreshBoi)->union($unitFreshBoi);
        });
        parent::boot();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function clientAccounts()
    {
        return $this->hasMany(ClientAccount::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function properties()
    {
        return $this->hasMany(Property::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function units()
    {
        return $this->hasMany(Unit::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function realUnits()
    {
        return $this->units()->where('units.is_virtual', '=', 0);
    }
}
