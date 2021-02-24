<?php

use Faker\Generator as Faker;

$factory->define(\App\Modules\Property\Models\PropertyTenure::class, function (Faker $faker) {
    $str = $faker->bothify('Tenure ##?#?');
    return [
        'name' => $str,
        'slug' => \App\Modules\Core\Library\StringHelper::slugify($str)
    ];
});
