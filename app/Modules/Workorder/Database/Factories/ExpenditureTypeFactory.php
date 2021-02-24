<?php

use Faker\Generator as Faker;

$factory->define(\App\Modules\Workorder\Models\ExpenditureType::class, function (Faker $faker) {
    return [
        'name' => $faker->word,
        'code' => $faker->ean13,
    ];
});
