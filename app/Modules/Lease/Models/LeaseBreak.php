<?php

namespace App\Modules\Lease\Models;

use App\Modules\Common\Classes\Abstracts\BaseModel;

class LeaseBreak extends BaseModel
{
    public static function getTableName(): string
    {
        return 'lease_breaks';
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'entity_id',
        'entity_type',
        'break_party_option_id',
        'penalty_incentive',
        'type',
        'date',
        'min_notice',
        'penalty',
        'notes'
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
    ];

    protected $with = [
        'breakPartyOption'
    ];

    public function breakPartyOption()
    {
        return $this->belongsTo(BreakPartyOption::class);
    }

    public function parent()
    {
        return $this->morphTo();
    }
}
