<?php

use Faker\Generator as Faker;

use \App\Modules\Property\Models\Property;
use \App\Modules\Workorder\Models\Quote;
use \App\Modules\Workorder\Models\Supplier;
use \App\Modules\Workorder\Models\ExpenditureType;

$factory->define(Quote::class, function (Faker $faker) {
    $property = Property::randomRow();
    $unit = $property->units()->first();
    $supplier = Supplier::randomRow();
    $expenditure_type = ExpenditureType::randomRow();
    return [
        'property_id' => $property->id,
        'unit_id' => $unit->id,
        'supplier_id' => $supplier->id,
        'expenditure_type_id' => $expenditure_type->id,
        'due_at' => $faker->dateTimeThisMonth(),
        'work_description' => $faker->sentence(),
        'value' => $faker->randomNumber(4),
    ];
});
