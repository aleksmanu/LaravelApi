<?php

use Faker\Generator as Faker;

$factory->define(\App\Modules\Lease\Models\Transaction::class, function (Faker $faker) {

    $transaction_type = \App\Modules\Lease\Models\TransactionType::inRandomOrder()->first();
    $paid_status      = \App\Modules\Lease\Models\PaidStatus::inRandomOrder()->first();
    return [
        'lease_charge_type_id'   => $transaction_type->id,
        'paid_status_id'        => $paid_status->id,
        'invoice_number'        => $faker->randomNumber() . 'x',
        'amount'                => $faker->randomFloat(2, 0, 10000),
        'vat'                   => $faker->randomFloat(2, 0, 10000),
        'gross'                 => $faker->randomFloat(2, 0, 10000),
        'gross_received'        => $faker->randomFloat(2, 0, 10000),
        'due_at'                => $faker->dateTimeBetween('-10 years', '+30 years'),
        'paid_at'               => $faker->dateTimeBetween('-10 years', '+30 years'),
        'period_from'           => $faker->dateTimeBetween('-10 years', '+30 years'),
        'period_to'             => $faker->dateTimeBetween('-10 years', '+30 years'),
        'yardi_transaction_ref' => $faker->randomNumber()
    ];
});
