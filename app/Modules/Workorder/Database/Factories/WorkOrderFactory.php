<?php

use Faker\Generator as Faker;
use App\Modules\Workorder\Models\WorkOrder;
use App\Modules\Workorder\Models\Quote;

$factory->define(WorkOrder::class, function (Faker $faker) {
    return [
        'value' => $faker->randomNumber(4),
    ];
});
