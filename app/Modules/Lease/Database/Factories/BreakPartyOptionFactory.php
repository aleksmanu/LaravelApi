<?php

use Faker\Generator as Faker;

$factory->define(\App\Modules\Lease\Models\BreakPartyOption::class, function (Faker $faker) {
    $str = $faker->bothify('BreakPartyOption ##?#?');
    return [
        'name' => $str,
        'slug' => str_replace(" ", "_", strtolower($str)),
    ];
});
