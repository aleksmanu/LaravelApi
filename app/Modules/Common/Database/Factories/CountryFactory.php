<?php

use Faker\Generator as Faker;

$factory->define(\App\Modules\Common\Models\Country::class, function (Faker $faker) {
    return [
        'name' => $faker->unique()->country,
    ];
});
