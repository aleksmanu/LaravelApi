<?php

use Faker\Generator as Faker;

$factory->define(\App\Modules\Lease\Models\RentFrequency::class, function (Faker $faker) {
    $str = $faker->bothify('RentFreq ##?#?');
    return [
        'name' => $str,
        'slug' => \App\Modules\Core\Library\StringHelper::slugify($str),
    ];
});
