<?php
namespace App\Modules\Lease\Models;

use App\Modules\Common\Classes\Abstracts\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class LeaseChargeType extends BaseModel
{
    use SoftDeletes;
    
    const RENT                 = 1;
    const ADDITIONAL_RENT      = 6;
    const GROUND_RENT          = 9;
    const RENT_NON_VAT         = 11;
    const LICENCE              = 12;
    const OTHER_CHARGE         = 13;
    const CHARGE_SERVICES      = 17;
    const SERVICE_CHARGE       = 21;
    const SF_INTERNAL_PAINTING = 22;
    const SF_EXTERNAL_PAINTING = 23;
    const SF_LIFT              = 25;
    const SF_INTERNAL_A        = 26;
    const SF_GENERAL           = 27;
    const RATES                = 30;
    const INSURANCE_BUILDINGS  = 41;
    const INSURANCE            = 44;
    const HL_INSURANCE         = 61;
    const PROPERTY_COSTS       = 75;
    const HL_CHARGES           = 78;
    const HL_RENT              = 81;
    const HL_SERVICE           = 82;

    const RENT_TYPES = [
        LeaseChargeType::HL_RENT,
        LeaseChargeType::RENT,
        LeaseChargeType::RENT_NON_VAT,
        LeaseChargeType::LICENCE,
        LeaseChargeType::ADDITIONAL_RENT,
        LeaseChargeType::GROUND_RENT,
    ];

    const SC_TYPES = [
        LeaseChargeType::HL_SERVICE,
        LeaseChargeType::SERVICE_CHARGE,
        LeaseChargeType::OTHER_CHARGE,
        LeaseChargeType::CHARGE_SERVICES,
        LeaseChargeType::SF_INTERNAL_PAINTING,
        LeaseChargeType::SF_EXTERNAL_PAINTING,
        LeaseChargeType::SF_LIFT,
        LeaseChargeType::SF_INTERNAL_A,
        LeaseChargeType::SF_GENERAL,
        LeaseChargeType::PROPERTY_COSTS,
    ];
    
    const INSURANCE_TYPES = [
        LeaseChargeType::HL_INSURANCE,
        LeaseChargeType::INSURANCE,
        LeaseChargeType::INSURANCE_BUILDINGS,
    ];
    
    const RATE_TYPES = [
        LeaseChargeType::HL_CHARGES,
        LeaseChargeType::RATES,
    ];

    /**
     * @return string
     */
    public static function getTableName(): string
    {
        return 'lease_charge_types';
    }


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
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
        'deleted_at'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function leaseCharges()
    {
        return $this->hasMany(LeaseCharge::class);
    }
}
