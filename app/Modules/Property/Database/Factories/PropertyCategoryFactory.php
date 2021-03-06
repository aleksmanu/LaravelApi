<?php

use Faker\Generator as Faker;

$factory->define(\App\Modules\Property\Models\PropertyCategory::class, function (Faker $faker) {
    $str = $faker->bothify('Property Category ##?#?');
    return [
        'name' => $str,
        'slug' => \App\Modules\Core\Library\StringHelper::slugify($str),
    ];
});
