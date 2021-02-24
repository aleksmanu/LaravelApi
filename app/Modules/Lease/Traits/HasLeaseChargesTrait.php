<?php
namespace App\Modules\Lease\Traits;

use App\Modules\Lease\Models\LeaseCharge;
use App\Modules\Lease\Models\LeaseChargeType;

trait HasLeaseChargesTrait
{
    public function leaseCharges()
    {
        return $this->morphMany(LeaseCharge::class, 'entity');
    }

    public function rentCharges()
    {
        return $this->leaseCharges()
            ->whereIn('lease_charge_type', LeaseChargeType::RENT_TYPES);
    }

    public function serviceCharges()
    {
        return $this->leaseCharges()
            ->whereIn('lease_charge_type', LeaseChargeType::SC_TYPES);
    }

    public function insuranceCharges()
    {
        return $this->leaseCharges()->whereIn('lease_charge_type', LeaseChargeType::INSURANCE_TYPES);
    }

    public function rateCharges()
    {
        return $this->leaseCharges()->whereIn('lease_charge_type', LeaseChargeType::RATE_TYPES);
    }

    public function getAnnualRentAttribute()
    {
        if ($this->rentCharges()->exists()) {
            return $this->rentCharges->sum('annual');
        }

        return 0;
    }

    public function getAnnualServiceChargeAttribute()
    {
        if ($this->serviceCharges()->exists()) {
            return $this->serviceCharges->sum('annual');
        }

        return 0;
    }

    public function getAnnualInsuranceChargeAttribute()
    {
        if ($this->insuranceCharges()->exists()) {
            return $this->insuranceCharges->sum('annual');
        }

        return 0;
    }

    public function getAnnualRateChargeAttribute()
    {
        if ($this->rateCharges()->exists()) {
            return $this->rateCharges->sum('annual');
        }

        return 0;
    }
}
