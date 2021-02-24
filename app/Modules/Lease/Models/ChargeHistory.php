<?php
namespace App\Modules\Lease\Models;

use App\Modules\Common\Classes\Abstracts\BaseModel;

class ChargeHistory extends BaseModel
{

    public static function getTableName(): string
    {
        return 'charge_history';
    }

    /**
     * @var array
     */
    protected $fillable = [
        'entity_id',
        'entity_type',
        'type_id',
        'amount',
        'reason',
        'created_at',
        'updated_at',
        'changed_on',
    ];

    /**
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
        'changed_on',
    ];

    /**
     * @var array
     */
    protected $casts = [
        'amount' => 'float'
    ];

    protected $with = [
        'leaseChargeType',
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
        return $this->belongsTo(LeaseChargeType::class, 'type_id', 'id');
    }
}
