<?php
namespace App\Modules\Property\Tests\Feature;

use App\Modules\Common\Classes\EndpointTest;
use App\Modules\Property\Models\PropertyStatus;

class PropertyStatusEndpointTest extends EndpointTest
{
    public function testCanUsersIndexPropertyStatuses()
    {
        $this->assertGetCountForAllUserTypes('/api/property/property-statuses/', PropertyStatus::query());
    }

    public function testCanUsersReadPropertyStatuses()
    {
        $existing_id = PropertyStatus::first()->id;
        $this->assertGetForAllUserTypes('/api/property/property-statuses/' . $existing_id);
        $this->apiAs($this->dev_user, 'GET', '/api/property/property-statuses/-1', [], [])->assertStatus(404);
    }

    public function testCanUsersCreatePropertyStatuses()
    {
        $this->apiAs($this->dev_user, 'POST', '/api/property/property-statuses/', [
            'name' => 'GottaMakeItImpossibleToCollideAmirite'
        ], [])->assertSuccessful();

        $this->assertInstanceOf(
            PropertyStatus::class,
            PropertyStatus::where('name', 'GottaMakeItImpossibleToCollideAmirite')->first()
        );
    }

    public function testCanUsersUpdatePropertyStatuses()
    {
        // Select a random record
        $unlucky_record = PropertyStatus::randomRow();
        $before_changes = clone $unlucky_record;

        // Make a patch request
        $this->apiAs($this->dev_user, 'PATCH', '/api/property/property-statuses/' . $unlucky_record->id, [
            'name' => 'ThisIsAPrefix' . $unlucky_record->name,
        ], [])->assertSuccessful();

        // Check if changes happened
        $unlucky_record = $unlucky_record->fresh();
        $this->assertEquals($unlucky_record->name, 'ThisIsAPrefix' . $before_changes->name);
    }

    public function testDoesPropertyUseControllerValidateProperly()
    {
        // Empty name will fail
        $this->apiAs($this->dev_user, 'POST', '/api/property/property-statuses/', [
            'name' => '',
        ], [])->assertJsonValidationErrors([
            'name'
        ]);

        // Proper name will succeed
        $this->apiAs($this->dev_user, 'POST', '/api/property/property-statuses/', [
            'name' => 'ThisWillDefinitelyBeADuplicate',
        ], [])->assertSuccessful();

        // Same name as before will fail as a duplicate
        $this->apiAs($this->dev_user, 'POST', '/api/property/property-statuses/', [
            'name' => 'ThisWillDefinitelyBeADuplicate',
        ], [])->assertJsonValidationErrors([
            'name'
        ]);
    }

    public function testCanUsersDeletePropertyStatuses()
    {
        $unlucky_record = (factory(PropertyStatus::class, 1)->create())->first();
        $this->assertInstanceOf(PropertyStatus::class, $unlucky_record);
        $this->apiAs(
            $this->dev_user,
            'DELETE',
            '/api/property/property-statuses/' . $unlucky_record->id,
            [],
            []
        )->assertSuccessful();
        $this->assertNull(PropertyStatus::find($unlucky_record->id));
    }
}
