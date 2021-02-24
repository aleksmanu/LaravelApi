<?php

use Faker\Generator as Faker;

$factory->define(\App\Modules\Property\Models\LocationType::class, function (Faker $faker) {
    $str = $faker->bothify('LocationType ##?#?');
    return [
        'name' => $str,
        'slug' => \App\Modules\Core\Library\StringHelper::slugify($str),
    ];
});
