<?php

namespace App\Modules\Lease\Database\Seeds;

use App\Modules\Lease\Models\Lease;
use App\Modules\Lease\Models\LeaseBreak;
use Illuminate\Database\Seeder;

class LeaseBreakSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach (Lease::all() as $lease) {
            factory(LeaseBreak::class, rand(getModelMagnitudeFactor(), 3 * getModelMagnitudeFactor()))->create(
                [
                    'entity_id' => $lease->id,
                    'entity_type' => get_class($lease),
                ]
            );
        }
    }
}
