<?php

use Faker\Generator as Faker;

$factory->define(\App\Modules\Client\Models\Portfolio::class, function (Faker $faker) {

    $data = [
        'yardi_portfolio_ref' => $faker->bothify('??#?#?##'),
        'name'                => $faker->word . ' ' . $faker->word,
    ];

    \App\Modules\Edits\Helpers\SeedHelper::setRandomReviewStatus($data);

    return $data;
});
