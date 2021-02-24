<?php
namespace App\Modules\Lease\Models;

use App\Modules\Common\Classes\Abstracts\BaseModel;
use App\Modules\Lease\Models\LeaseChargeType;

class Transaction extends BaseModel
{
    public static function getTableName(): string
    {
        return 'transactions';
    }

    /**
     * @var array
     */
    protected $fillable = [
        'lease_id',
        'lease_type',
        'lease_charge_type_id',
        'paid_status_id',
        'yardi_transaction_ref',
        'invoice_number',
        'amount',
        'vat',
        'gross',
        'gross_received',
        'due_at',
        'paid_at',
        'period_from',
        'period_to',
        'description',
        'o2_lease_payable_reference',
        'supplier_number',
        'supplier_name',
        'ouc_code',
        'apb_normal',
        'o2_gl_code',
        'apb_property_reference',
    ];

    /**
     * @var array
     */
    protected $dates = [
        'due_at',
        'paid_at',
        'period_from',
        'period_to',
        'created_at',
        'updated_at'
    ];

    /**
     * @var array
     */
    protected $with = [
        'paidStatus',
        'leaseChargeType',
    ];

    /**
     * @var array
     */
    protected $casts = [
        'amount'         => 'float',
        'vat'            => 'float',
        'gross'          => 'float',
        'gross_received' => 'float'
    ];

    public function lease()
    {
        return $this->morphTo();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function leaseChargeType()
    {
        return $this->belongsTo(LeaseChargeType::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function paidStatus()
    {
        return $this->belongsTo(PaidStatus::class);
    }
}
