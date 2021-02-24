<?php

use Faker\Generator as Faker;

$factory->define(\App\Modules\Property\Models\Unit::class, function (Faker $faker) {

    $user = (\App\Modules\Auth\Models\User::randomRow());

    $data = [
        'measurement_unit_id'     => (\App\Modules\Property\Models\MeasurementUnit::randomRow())->id,
        'demise'                  => $faker->words(rand(2, 5), true),
        'unit'                    => $faker->bothify('#??###?#?'),
        'name'                    => $faker->words(rand(2, 5), true),
        'measurement_value'       => $faker->randomFloat(),
        'yardi_property_unit_ref' => $faker->bothify('#??###?#?'),
        'yardi_unit_ref'          => $faker->bothify('#??###?#?'),
        'yardi_import_ref'        => $faker->bothify('#??###?#?'),
        'approved'                => true,
        'approved_at'             => $faker->dateTimeBetween('-10 years', '+30 years'),
        'approved_initials'       => $user->calculateInitials(),
        'held_at'                 => $faker->dateTimeBetween('-10 years', '+30 years'),
        'held_initials'           => $user->calculateInitials(),
    ];

    \App\Modules\Edits\Helpers\SeedHelper::setRandomReviewStatus($data);

    return $data;
});
