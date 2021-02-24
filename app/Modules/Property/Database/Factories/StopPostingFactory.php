<?php

use Faker\Generator as Faker;

$factory->define(\App\Modules\Property\Models\StopPosting::class, function (Faker $faker) {
    $str = $faker->bothify('StopPosting ##?#?');
    return [
        'name' => $str,
        'slug' => \App\Modules\Core\Library\StringHelper::slugify($str),
    ];
});
