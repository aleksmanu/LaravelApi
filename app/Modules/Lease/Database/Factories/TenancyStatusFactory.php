<?php

use Faker\Generator as Faker;

$factory->define(\App\Modules\Lease\Models\TenantStatus::class, function (Faker $faker) {
    $str = $faker->bothify('TenantStatus ##?#?');
    return [
        'name' => $str,
        'slug' => \App\Modules\Core\Library\StringHelper::slugify($str)
    ];
});
