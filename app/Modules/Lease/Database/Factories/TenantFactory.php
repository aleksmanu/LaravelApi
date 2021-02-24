<?php

use Faker\Generator as Faker;

$factory->define(\App\Modules\Lease\Models\Tenant::class, function (Faker $faker) {
    $data = [
        'tenant_status_id'     => (\App\Modules\Lease\Models\TenantStatus::randomRow())->id,
        'lease_id'             => (\App\Modules\Lease\Models\Lease::randomRow())->id,
        'name'                 => $faker->words(rand(2, 5), true),
        'yardi_tenant_ref'     => $faker->bothify('?##?#?#'),
        'yardi_tenant_alt_ref' => $faker->bothify('?##?#?#'),
    ];

    \App\Modules\Edits\Helpers\SeedHelper::setRandomReviewStatus($data);

    return $data;
});
