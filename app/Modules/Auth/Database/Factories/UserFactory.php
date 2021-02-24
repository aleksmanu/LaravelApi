<?php

use Faker\Generator as Faker;

$factory->define(\App\Modules\Auth\Models\User::class, function (Faker $faker) {
    $account = \App\Modules\Account\Models\Account::where('name', '=', 'SYSTEM')->inRandomOrder()->first();
    return [
        'account_id' => $account->id,
        'first_name' => $faker->firstName,
        'last_name'  => $faker->lastName,
        'email'      => microtime(true) . $faker->unique()->safeEmail,
        'password'   => \Illuminate\Support\Facades\Hash::make($faker->word),
    ];
});
