<?php
namespace App\Modules\Property\Tests\Feature;

use App\Modules\Common\Classes\EndpointTest;
use App\Modules\Property\Models\PropertyUse;

class PropertyUseEndpointTest extends EndpointTest
{
    public function testCanUsersIndexPropertyUses()
    {
        $this->assertGetCountForAllUserTypes('/api/property/property-uses/', PropertyUse::query());
    }

    public function testCanUsersReadPropertyUses()
    {
        $existing_id = PropertyUse::first()->id;
        $this->assertGetForAllUserTypes('/api/property/property-uses/' . $existing_id);
        $this->apiAs($this->dev_user, 'GET', '/api/property/property-uses/-1', [], [])->assertStatus(404);
    }

    public function testCanUsersCreatePropertyUses()
    {
        $this->apiAs($this->dev_user, 'POST', '/api/property/property-uses/', [
            'name' => 'GottaMakeItImpossibleToCollideAmirite'
        ], [])->assertSuccessful();

        $this->assertInstanceOf(
            PropertyUse::class,
            PropertyUse::where('name', 'GottaMakeItImpossibleToCollideAmirite')->first()
        );
    }

    public function testCanUsersUpdatePropertyUses()
    {
        // Select a random record
        $unlucky_record = PropertyUse::randomRow();
        $before_changes = clone $unlucky_record;

        // Make a patch request
        $this->apiAs($this->dev_user, 'PATCH', '/api/property/property-uses/' . $unlucky_record->id, [
            'name' => 'ThisIsAPrefix' . $unlucky_record->name,
        ], [])->assertSuccessful();

        // Check if changes happened
        $unlucky_record = $unlucky_record->fresh();
        $this->assertEquals($unlucky_record->name, 'ThisIsAPrefix' . $before_changes->name);
    }

    public function testDoesPropertyUsesControllerValidateProperly()
    {
        // Empty name will fail
        $this->apiAs($this->dev_user, 'POST', '/api/property/property-uses/', [
            'name' => '',
        ], [])->assertJsonValidationErrors([
            'name'
        ]);

        // Proper name will succeed
        $this->apiAs($this->dev_user, 'POST', '/api/property/property-uses/', [
            'name' => 'ThisWillDefinitelyBeADuplicate',
        ], [])->assertSuccessful();

        // Same name as before will fail as a duplicate
        $this->apiAs($this->dev_user, 'POST', '/api/property/property-uses/', [
            'name' => 'ThisWillDefinitelyBeADuplicate',
        ], [])->assertJsonValidationErrors([
            'name'
        ]);
    }

    public function testCanUsersDeletePropertyUses()
    {
        $unlucky_record = (factory(PropertyUse::class, 1)->create())->first();
        $this->assertInstanceOf(PropertyUse::class, $unlucky_record);
        $this->apiAs(
            $this->dev_user,
            'DELETE',
            '/api/property/property-uses/' . $unlucky_record->id,
            [],
            []
        )->assertSuccessful();
        $this->assertNull(PropertyUse::find($unlucky_record->id));
    }
}
