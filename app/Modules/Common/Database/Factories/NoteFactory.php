<?php

use Faker\Generator as Faker;

$factory->define(\App\Modules\Common\Models\Note::class, function (Faker $faker) {

    $user_id = \App\Modules\Auth\Models\User::inRandomOrder()->first()->id;
    return [
        'user_id' => $user_id,
        'note'    => $faker->paragraph,
    ];
});
