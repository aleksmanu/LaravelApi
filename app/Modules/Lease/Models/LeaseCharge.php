<?php
namespace App\Modules\Lease\Models;

use App\Modules\Common\Classes\Abstracts\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class LeaseCharge extends BaseModel
{
    use SoftDeletes;

    public static function getTableName(): string
    {
        return 'lease_charges';
    }

    /**
     * @var array
     */
    protected $fillable = [
        'entity_id',
        'entity_type',
        'lease_charge_type',

        'pay_terms',
        'charge_from',
        'start',

        'li_charged',
        'li_grace',
        'li_bank',
        'li_rate',
        'li_accrued',
        'li_min_rate',
        'li_max_rate',

        'vat',
        'frequency',
        'annual',
        'period',

        'created_at',
        'updated_at',
        'deleted_at',

        'payment_by',
        'end',
        'commencement',
        'pay_method',
        'supplier_ref',
        'freq_next',
    ];

    /**
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
        'freq_next',
        'end',
        'commencement',
    ];

    /**
     * @var array
     */
    protected $casts = [
        'annual' => 'float'
    ];

    protected $with = [
        'leaseChargeType'
    ];

    public function entity()
    {
        return $this->morphTo();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function leaseChargeType()
    {
        return $this->belongsTo(LeaseChargeType::class, 'lease_charge_type', 'id');
    }
}
