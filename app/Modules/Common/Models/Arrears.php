<?php
namespace App\Modules\Common\Models;

use App\Modules\Common\Classes\Abstracts\BaseModel;
use App\Modules\Lease\Models\LeaseChargeType;

class Arrears extends BaseModel
{
    /**
     * @return string
     */
    public static function getTableName(): string
    {
        return 'arrears';
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'charge_type_id',
        'lease_id',
        'lease_type',
        'invoice_number',
        'description',
        'due_date',
        'period_from',
        'period_to',
    ];

    protected $hidden = [];

    protected $dates = [
        'due_date',
        'period_from',
        'period_to',
    ];

    protected $casts = [];

    protected $appends = [];

    protected $with = [];

    public function lease()
    {
        return $this->morphTo();
    }

    public function leaseChargeType()
    {
        return $this->belongsTo(LeaseChargeType::class, 'charge_type_id', 'id');
    }
}
