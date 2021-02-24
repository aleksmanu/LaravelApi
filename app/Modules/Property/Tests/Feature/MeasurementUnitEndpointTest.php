<?php
namespace App\Modules\Property\Tests\Feature;

use App\Modules\Common\Classes\EndpointTest;
use App\Modules\Property\Models\MeasurementUnit;

class MeasurementUnitEndpointTest extends EndpointTest
{
    public function testCanUsersIndexMeasurementUnits()
    {
        $this->assertGetCountForAllUserTypes('/api/property/measurement-units/', MeasurementUnit::query());
    }

    public function testCanUsersReadMeasurementUnits()
    {
        $existing_id = MeasurementUnit::first()->id;
        $this->assertGetForAllUserTypes('/api/property/measurement-units/' . $existing_id);
        $this->apiAs($this->dev_user, 'GET', '/api/property/measurement-units/-1', [], [])->assertStatus(404);
    }

    public function testCanUsersCreateMeasurementUnits()
    {
        $this->apiAs($this->dev_user, 'POST', '/api/property/measurement-units/', [
            'name' => 'GottaMakeItImpossibleToCollideAmirite',
        ], [])->assertSuccessful();

        $this->assertInstanceOf(
            MeasurementUnit::class,
            MeasurementUnit::where('name', 'GottaMakeItImpossibleToCollideAmirite')->first()
        );
    }

    public function testCanUsersUpdateMeasurementUnits()
    {
        // Select a random record
        $unlucky_record = MeasurementUnit::randomRow();
        $before_changes = clone $unlucky_record;

        // Make a patch request
        $this->apiAs($this->dev_user, 'PATCH', '/api/property/measurement-units/' . $unlucky_record->id, [
            'name' => 'ThisIsAPrefix' . $unlucky_record->name,
        ], [])->assertSuccessful();

        // Check if changes happened
        $unlucky_record = $unlucky_record->fresh();
        $this->assertEquals($unlucky_record->name, 'ThisIsAPrefix' . $before_changes->name);
    }

    public function testDoesMeasurementUnitsControllerValidateProperly()
    {
        // Empty name will fail
        $this->apiAs($this->dev_user, 'POST', '/api/property/measurement-units/', [
            'name' => '',
        ], [])->assertJsonValidationErrors([
            'name',
        ]);

        // Proper name will succeed
        $this->apiAs($this->dev_user, 'POST', '/api/property/measurement-units/', [
            'name' => 'ThisWillDefinitelyBeADuplicate',
        ], [])->assertSuccessful();

        // Same name as before will fail as a duplicate
        $this->apiAs($this->dev_user, 'POST', '/api/property/measurement-units/', [
            'name' => 'ThisWillDefinitelyBeADuplicate',
        ], [])->assertJsonValidationErrors([
            'name'
        ]);
    }

    public function testCanUsersDeleteMeasurementUnits()
    {
        $unlucky_record = MeasurementUnit::create(['name' => 'test_imperial_toothwidths']);
        $this->assertInstanceOf(MeasurementUnit::class, $unlucky_record);
        $this->apiAs(
            $this->dev_user,
            'DELETE',
            '/api/property/measurement-units/' . $unlucky_record->id,
            [],
            []
        )->assertSuccessful();
        $this->assertNull(MeasurementUnit::find($unlucky_record->id));
    }
}
