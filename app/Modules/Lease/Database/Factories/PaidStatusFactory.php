<?php

use Faker\Generator as Faker;

$factory->define(\App\Modules\Lease\Models\PaidStatus::class, function (Faker $faker) {

    $str = $faker->bothify('PaidStatus ##?#?');
    return [
        'name' => $str,
    ];
});
