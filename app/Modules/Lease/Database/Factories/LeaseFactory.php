<?php

use Faker\Generator as Faker;

$factory->define(\App\Modules\Lease\Models\Lease::class, function (Faker $faker) {

    $user = (\App\Modules\Auth\Models\User::randomRow());
    $data = [
        'annual_rent_vat_rate'           => rand(0, 20),
        'annual_service_charge_vat_rate' => rand(0, 20),
        'lease_type_id'                  => (\App\Modules\Lease\Models\LeaseType::randomRow())->id,
        'break_party_option_id'          => (\App\Modules\Lease\Models\BreakPartyOption::randomRow())->id,
        'rent_frequency_id'              => (\App\Modules\Lease\Models\RentFrequency::randomRow())->id,
        'review_type_id'                 => (\App\Modules\Lease\Models\ReviewType::randomRow())->id,
        'break_notice_days'              => $faker->randomNumber(),
//        'annual_rent'                    => $faker->randomFloat(2, 500, 10000),
//        'annual_service_charge'          => $faker->randomFloat(2, 50, 1000),
        'live'                           => 1,
        'next_review_at'                 => $faker->dateTimeBetween('-10 years', '+30 years'),
//        'next_break_at'                  => $faker->dateTimeBetween('-10 years', '+30 years'),
        'commencement_at'                => $faker->dateTimeBetween('-10 years', '+30 years'),
        'expiry_at'                      => $faker->dateTimeBetween('-10 years', '+30 years'),
        'approved'                       => true,
        'approved_at'                    => $faker->dateTimeBetween('-10 years', '+30 years'),
        'approved_initials'              => $user->calculateInitials(),
        'held_at'                        => $faker->dateTimeBetween('-10 years', '+30 years'),
        'held_initials'                  => $user->calculateInitials(),
        'yardi_tenant_ref'               => $faker->bothify('?##?#?#'),
    ];

    \App\Modules\Edits\Helpers\SeedHelper::setRandomReviewStatus($data);

    return $data;
});
