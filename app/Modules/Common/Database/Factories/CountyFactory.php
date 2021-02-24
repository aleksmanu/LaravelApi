<?php

use Faker\Generator as Faker;

$factory->define(\App\Modules\Common\Models\County::class, function (Faker $faker) {
    return [
        'name' => $faker->state,
    ];
});
