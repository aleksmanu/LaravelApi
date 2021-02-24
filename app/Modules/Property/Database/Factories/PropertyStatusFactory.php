<?php

use Faker\Generator as Faker;

$factory->define(\App\Modules\Property\Models\PropertyStatus::class, function (Faker $faker) {
    $str = $faker->bothify('PropStatus ##?#?');
    return [
        'name' => $str,
        'slug' => \App\Modules\Core\Library\StringHelper::slugify($str),
    ];
});
