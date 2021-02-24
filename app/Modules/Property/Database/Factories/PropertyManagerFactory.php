<?php

use Faker\Generator as Faker;

$factory->define(\App\Modules\Property\Models\PropertyManager::class, function (Faker $faker) {
    $random_nonmanager_user = \App\Modules\Auth\Models\User::whereNotIn('id', function ($query) {
        $query->select('user_id')->from(\App\Modules\Property\Models\PropertyManager::getTableName());
    })->first();

    return [
        'user_id' => $random_nonmanager_user->id,
        'code' => $faker->bothify('?#?#?#'),
    ];
});
