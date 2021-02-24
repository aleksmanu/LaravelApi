<?php
namespace App\Modules\Common\Models;

use App\Modules\Common\Classes\Abstracts\BaseModel;
use App\Modules\Lease\Models\Lease;

class Agent extends BaseModel
{
    /**
     * @return string
     */
    public static function getTableName(): string
    {
        return 'agents';
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'type',
        'address_id',
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
    ];

    protected $casts = [
    ];

    /**
     * @var array
     */
    protected $appends = [
    ];

    protected $with = [
        'address',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function leases()
    {
        return $this->hasMany(Lease::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function address()
    {
        return $this->belongsTo(Address::class);
    }
}
