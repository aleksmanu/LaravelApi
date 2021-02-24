<?php
namespace App\Modules\Property\Tests\Feature;

use App\Modules\Common\Classes\EndpointTest;
use App\Modules\Property\Models\LocationType;

class LocationTypeEndpointTest extends EndpointTest
{
    public function testCanUsersIndexLocationTypes()
    {
        $this->assertGetCountForAllUserTypes('/api/property/location-types/', LocationType::query());
    }

    public function testCanUsersReadLocationTypes()
    {
        $existing_id = LocationType::first()->id;
        $this->assertGetForAllUserTypes('/api/property/location-types/' . $existing_id);
        $this->apiAs($this->dev_user, 'GET', '/api/property/location-types/-1', [], [])->assertStatus(404);
    }

    public function testCanUsersCreateLocationTypes()
    {
        $this->apiAs($this->dev_user, 'POST', '/api/property/location-types/', [
            'name' => 'GottaMakeItImpossibleToCollideAmirite'
        ], [])->assertSuccessful();

        $this->assertInstanceOf(
            LocationType::class,
            LocationType::where('name', 'GottaMakeItImpossibleToCollideAmirite')->first()
        );
    }

    public function testCanUsersUpdateLocationTypes()
    {
        // Select a random record
        $unlucky_record = LocationType::randomRow();
        $before_changes = clone $unlucky_record;

        // Make a patch request
        $this->apiAs($this->dev_user, 'PATCH', '/api/property/location-types/' . $unlucky_record->id, [
            'name' => 'ThisIsAPrefix' . $unlucky_record->name,
        ], [])->assertSuccessful();

        // Check if changes happened
        $unlucky_record = $unlucky_record->fresh();
        $this->assertEquals($unlucky_record->name, 'ThisIsAPrefix' . $before_changes->name);
    }

    public function testDoesLocationTypesControllerValidateProperly()
    {
        // Empty name will fail
        $this->apiAs($this->dev_user, 'POST', '/api/property/location-types/', [
            'name' => '',
        ], [])->assertJsonValidationErrors([
            'name'
        ]);

        // Proper name will succeed
        $this->apiAs($this->dev_user, 'POST', '/api/property/location-types/', [
            'name' => 'ThisWillDefinitelyBeADuplicate',
        ], [])->assertSuccessful();

        // Same name as before will fail as a duplicate
        $this->apiAs($this->dev_user, 'POST', '/api/property/location-types/', [
            'name' => 'ThisWillDefinitelyBeADuplicate',
        ], [])->assertJsonValidationErrors([
            'name'
        ]);
    }

    public function testCanUsersDeleteLocationTypes()
    {
        $unlucky_record = (factory(LocationType::class, 1)->create())->first();
        $this->assertInstanceOf(LocationType::class, $unlucky_record);
        $this->apiAs(
            $this->dev_user,
            'DELETE',
            '/api/property/location-types/' . $unlucky_record->id,
            [],
            []
        )->assertSuccessful();
        $this->assertNull(LocationType::find($unlucky_record->id));
    }
}
