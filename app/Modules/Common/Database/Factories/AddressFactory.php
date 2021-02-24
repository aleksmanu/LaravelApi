<?php

use Faker\Generator as Faker;

$factory->define(\App\Modules\Common\Models\Address::class, function (Faker $faker) {
    $data = [
        'county_id'  => (\App\Modules\Common\Models\County::randomRow())->id,
        'country_id' => (\App\Modules\Common\Models\Country::randomRow())->id,
        'unit'       => $faker->secondaryAddress,
        'building'   => $faker->word . ' Building',
        'number'     => $faker->buildingNumber,
        'street'     => $faker->streetName,
        'estate'     => $faker->word,
        'suburb'     => $faker->word,
        'town'       => $faker->city,
        'postcode'   => $faker->postcode,
        'latitude'   => $faker->latitude($min = 51, $max = 58),
        'longitude'  => $faker->longitude($min = -7, $max = 0.5),
    ];

    \App\Modules\Edits\Helpers\SeedHelper::setRandomReviewStatus($data);

    return $data;
});
