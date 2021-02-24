<?php

use Faker\Generator as Faker;

$factory->define(\App\Modules\Lease\Models\TransactionType::class, function (Faker $faker) {

    $str = $faker->bothify('PaidStatus ##?#?');
    return [
        'name' => $str,
        'code' => $faker->randomNumber(9)
    ];
});
