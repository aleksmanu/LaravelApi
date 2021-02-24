<?php

use Faker\Generator as Faker;

$factory->define(\App\Modules\Lease\Models\LeaseBreak::class, function (Faker $faker) {
    
    $foreign = \App\Modules\Lease\Models\Lease::randomRow();

    return [
        'break_party_option_id' => (\App\Modules\Lease\Models\BreakPartyOption::randomRow())->id,
        'type' => $faker->words(rand(1, 4), true),
        'date' => $faker->dateTimeBetween('+1 years', '+5 years'),
        'min_notice' => rand(1, 99),
        'penalty' => rand(0, 1),
        'penalty_incentive' => $faker->sentence,
        'notes' => $faker->sentence()
    ];
});
