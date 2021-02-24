<?php

use Faker\Generator as Faker;

$factory->define(\App\Modules\Account\Models\Account::class, function (Faker $faker) {

    $account_type = \App\Modules\Account\Models\AccountType::where(
        'slug',
        \App\Modules\Account\Models\AccountType::CLIENT
    )->first();

    return [
        'account_type_id' => $account_type->id,
        'name'            => $faker->company . ' ' . $faker->companySuffix,
    ];
});
