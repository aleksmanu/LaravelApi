<?php

use Faker\Generator as Faker;

$factory->define(\App\Modules\Property\Models\Property::class, function (Faker $faker) {

    $user = (\App\Modules\Auth\Models\User::randomRow());

    $data = [
        'address_id'                => (\App\Modules\Common\Models\Address::randomRow())->id,
        'property_status_id'        => (\App\Modules\Property\Models\PropertyStatus::randomRow())->id,
        'property_use_id'           => (\App\Modules\Property\Models\PropertyUse::randomRow())->id,
        'property_tenure_id'        => (\App\Modules\Property\Models\PropertyTenure::randomRow())->id,
        'location_type_id'          => (\App\Modules\Property\Models\LocationType::randomRow())->id,
        'property_category_id'      => (\App\Modules\Property\Models\PropertyCategory::randomRow())->id,
        'stop_posting_id'           => (\App\Modules\Property\Models\StopPosting::randomRow())->id,
        'name'                      => $faker->words(rand(3, 5), true),
        'yardi_property_ref'        => $faker->bothify('##?#?#?'),
        'yardi_alt_ref'             => $faker->bothify('##?#?#?'),
        'total_lettable_area'       => $faker->randomFloat(2, 1, 999999),
        'void_total_lettable_area'  => $faker->randomFloat(2, 1, 999999),
        'total_site_area'           => $faker->randomFloat(2, 1, 999999),
        'total_gross_internal_area' => $faker->randomFloat(2, 1, 999999),
        'total_rateable_value'      => $faker->randomFloat(2, 1, 999999),
        'void_total_rateable_value' => $faker->randomFloat(2, 1, 999999),
        'listed_building'           => rand(0, 1),
        'live'                      => rand(0, 1),
        'conservation_area'         => rand(0, 1),
        'air_conditioned'           => rand(0, 1),
        'vat_registered'            => rand(0, 1),
        'held_at'                   => $faker->dateTimeBetween('-10 years', '+30 years'),
        'held_initials'             => $user->calculateInitials(),
        'approved'                  => rand(0, 1),
        'approved_at'               => $faker->dateTimeBetween('-10 years', '+30 years'),
        'approved_initials'         => $user->calculateInitials()
    ];

    \App\Modules\Edits\Helpers\SeedHelper::setRandomReviewStatus($data);

    return $data;
});
