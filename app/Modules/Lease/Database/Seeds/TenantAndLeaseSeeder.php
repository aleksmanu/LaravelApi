<?php

namespace App\Modules\Lease\Database\Seeds;

use App\Modules\Lease\Models\BreakPartyOption;
use App\Modules\Lease\Models\LeaseType;
use App\Modules\Lease\Models\PaidStatus;
use App\Modules\Lease\Models\RentFrequency;
use App\Modules\Lease\Models\ReviewType;
use App\Modules\Lease\Models\Tenant;
use App\Modules\Lease\Models\Lease;
use App\Modules\Lease\Models\TenantStatus;
use App\Modules\Lease\Models\Transaction;
use App\Modules\Lease\Models\TransactionType;
use App\Modules\Property\Models\Unit;
use Illuminate\Database\Seeder;

class TenantAndLeaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(TenantStatus::class, rand(5 + getModelMagnitudeFactor(), 5 + getModelMagnitudeFactor()))->create();
        factory(RentFrequency::class, rand(5 + getModelMagnitudeFactor(), 5 + getModelMagnitudeFactor()))->create();
        factory(ReviewType::class, rand(5 + getModelMagnitudeFactor(), 5 + getModelMagnitudeFactor()))->create();
        factory(LeaseType::class, rand(5 + getModelMagnitudeFactor(), 5 + getModelMagnitudeFactor()))->create();
        factory(BreakPartyOption::class, rand(5 + getModelMagnitudeFactor(), 5 + getModelMagnitudeFactor()))->create();
        factory(PaidStatus::class, rand(5 + getModelMagnitudeFactor(), 5 + getModelMagnitudeFactor()))->create();
        factory(TransactionType::class, rand(5 + getModelMagnitudeFactor(), 5 + getModelMagnitudeFactor()))->create();

        foreach (Unit::all() as $unit) {
            if (rand(0, 3)) { // 25% chance to not create
                $tmpLease = factory(Lease::class, 1)->create(
                    [
                        'unit_id'               => $unit->id,
                        'yardi_import_unit_ref' => $unit->yardi_import_ref
                    ]
                );

                factory(Tenant::class, 1)->create([
                    'lease_id' => $tmpLease->first()->id
                ]);
            }
        }

        foreach (Lease::all() as $lease) {
            factory(Transaction::class, rand(1, 5))->create(['lease_id' => $lease->id]);
        }
    }
}
