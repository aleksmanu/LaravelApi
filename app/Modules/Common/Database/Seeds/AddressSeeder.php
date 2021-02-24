<?php

namespace App\Modules\Common\Database\Seeds;

use Illuminate\Database\Seeder;

class AddressSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(\App\Modules\Common\Models\Country::class, rand(1, 10))->create();
        factory(\App\Modules\Common\Models\County::class, rand(1, 10))->create();
        factory(
            \App\Modules\Common\Models\Address::class,
            rand(
                10 * getModelMagnitudeFactor(),
                15 * getModelMagnitudeFactor()
            )
        )->create();
    }
}
