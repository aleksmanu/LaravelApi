<?php

use Faker\Generator as Faker;

$factory->define(\App\Modules\Workorder\Models\Supplier::class, function (Faker $faker) {
    return [
        'name' => $faker->company,
        'email' => $faker->email,
        'phone' => $faker->phoneNumber,
    ];
});
