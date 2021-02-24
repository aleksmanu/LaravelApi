<?php

use Faker\Generator as Faker;

$factory->define(\App\Modules\Lease\Models\ReviewType::class, function (Faker $faker) {
    $str = $faker->bothify('ReviewType ##?#?');
    return [
        'name' => $str,
        'slug' => \App\Modules\Core\Library\StringHelper::slugify($str),
    ];
});
