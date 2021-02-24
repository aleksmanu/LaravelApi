<?php

namespace App\Modules\Property\Database\Seeds;

use App\Modules\Client\Models\Portfolio;
use App\Modules\Property\Models\LocationType;
use App\Modules\Property\Models\MeasurementUnit;
use App\Modules\Property\Models\Property;
use App\Modules\Property\Models\PropertyCategory;
use App\Modules\Property\Models\PropertyStatus;
use App\Modules\Property\Models\PropertyUse;
use App\Modules\Property\Models\PropertyTenure;
use App\Modules\Property\Models\StopPosting;
use App\Modules\Property\Models\Unit;
use Illuminate\Database\Seeder;

class PropertySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        MeasurementUnit::create(['name' => 'Square Meters']);
        MeasurementUnit::create(['name' => 'Square Feet']);

        factory(PropertyStatus::class, rand(5 + getModelMagnitudeFactor(), 5 + getModelMagnitudeFactor()))->create();
        factory(PropertyUse::class, rand(5 + getModelMagnitudeFactor(), 5 + getModelMagnitudeFactor()))->create();
        factory(PropertyTenure::class, rand(5 + getModelMagnitudeFactor(), 10 + getModelMagnitudeFactor()))->create();
        factory(LocationType::class, rand(5 + getModelMagnitudeFactor(), 5 + getModelMagnitudeFactor()))->create();
        factory(PropertyCategory::class, rand(5 + getModelMagnitudeFactor(), 5 + getModelMagnitudeFactor()))->create();
        factory(StopPosting::class, rand(5 + getModelMagnitudeFactor(), 5 + getModelMagnitudeFactor()))->create();

        foreach (Portfolio::all() as $portfolio) {
            factory(
                Property::class,
                rand(
                    1 + getModelMagnitudeFactor()/3,
                    5 + getModelMagnitudeFactor()/3
                )
            )->create([
                'portfolio_id'        => $portfolio->id,
                'property_manager_id' => $portfolio->clientAccount->property_manager_id
            ]);
        }

        foreach (Property::all() as $property) {
            factory(
                Unit::class,
                rand(
                    2 + getModelMagnitudeFactor()/2,
                    10 + getModelMagnitudeFactor()/2
                )
            )->create([
                'property_id'         => $property->id,
                'property_manager_id' => $property->property_manager_id
            ]);
        }
    }
}
