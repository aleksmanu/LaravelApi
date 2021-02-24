<?php

use Faker\Generator as Faker;

$factory->define(\App\Modules\Lease\Models\LeaseType::class, function (Faker $faker) {
    $str = $faker->bothify('LeaseType ##?#?');
    return [
        'name' => $str,
        'slug' => \App\Modules\Core\Library\StringHelper::slugify($str),
    ];
});
