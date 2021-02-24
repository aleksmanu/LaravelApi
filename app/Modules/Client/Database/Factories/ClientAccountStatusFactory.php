<?php

use Faker\Generator as Faker;

$factory->define(\App\Modules\Client\Models\ClientAccountStatus::class, function (Faker $faker) {
    $str = $faker->bothify('Status ##?#?');
    return [
        'name' => $str,
        'slug' => str_replace(" ", "_", strtolower($str)),
    ];
});
