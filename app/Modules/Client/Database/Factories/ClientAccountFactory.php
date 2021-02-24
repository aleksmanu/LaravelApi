<?php

use Faker\Generator as Faker;

$factory->define(\App\Modules\Client\Models\ClientAccount::class, function (Faker $faker) {

    $data = [
        'account_id'               => (
            \App\Modules\Account\Models\Account::where('name', '!=', 'SYSTEM')
                ->where('name', '!=', 'EXTERNAL')
                ->inRandomOrder()
                ->first()
        )->id,
        'organisation_type_id'     => (\App\Modules\Client\Models\OrganisationType::randomRow())->id,
        'address_id'               => (\App\Modules\Common\Models\Address::randomRow())->id,
        'property_manager_id'      => (\App\Modules\Property\Models\PropertyManager::randomRow())->id,
        'client_account_status_id' => (\App\Modules\Client\Models\ClientAccountStatus::randomRow())->id,
        'name'                     => $faker->company,
        'yardi_client_ref'         => $faker->bothify('?#?#?#?#'),
        'yardi_alt_ref'            => $faker->bothify('?#?#?#?#'),
    ];

    \App\Modules\Edits\Helpers\SeedHelper::setRandomReviewStatus($data);

    return $data;
});
