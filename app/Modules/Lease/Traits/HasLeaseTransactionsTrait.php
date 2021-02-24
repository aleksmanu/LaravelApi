<?php
namespace App\Modules\Lease\Traits;

use App\Modules\Lease\Models\Transaction;
use App\Modules\Lease\Models\LeaseChargeType;

trait HasLeaseTransactionsTrait
{
    public function leaseTransactions()
    {
        return $this->morphMany(Transaction::class, 'lease');
    }

    public function rentTransactions()
    {
        return $this->leaseTransactions()->whereIn('lease_charge_type_id', LeaseChargeType::RENT_TYPES);
    }

    public function serviceTransactions()
    {
        return $this->leaseTransactions()->whereIn('lease_charge_type_id', LeaseChargeType::SC_TYPES);
    }

    public function insuranceTransactions()
    {
        return $this->leaseTransactions()->whereIn('lease_charge_type_id', LeaseChargeType::INSURANCE_TYPES);
    }

    public function rateTransactions()
    {
        return $this->leaseTransactions()->whereIn('lease_charge_type_id', LeaseChargeType::RATE_TYPES);
    }
}
