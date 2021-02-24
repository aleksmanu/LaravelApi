<?php

use Faker\Generator as Faker;

$factory->define(\App\Modules\Property\Models\PropertyUse::class, function (Faker $faker) {
    $str = $faker->bothify('Use ##?#?');
    return [
        'name' => $str,
        'slug' => \App\Modules\Core\Library\StringHelper::slugify($str),
    ];
});
