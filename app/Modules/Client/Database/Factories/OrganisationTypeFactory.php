<?php

use Faker\Generator as Faker;

$factory->define(\App\Modules\Client\Models\OrganisationType::class, function (Faker $faker) {
    return [
        'name' => $faker->numerify('OrgType ##'),
    ];
});
