<?php
namespace App\Modules\Property\Tests\Feature;

use App\Modules\Common\Classes\EndpointTest;
use App\Modules\Common\Traits\TestsEndpoints;
use App\Modules\Property\Models\PropertyTenure;
use Tests\TestCase;

class TenureEndpointTest extends EndpointTest
{
    public function testCanUsersIndexTenures()
    {
        $this->assertGetCountForAllUserTypes('/api/property/property-tenures/', PropertyTenure::query());
    }

    public function testCanUsersReadTenures()
    {
        $existing_id = PropertyTenure::first()->id;
        $this->assertGetForAllUserTypes('/api/property/property-tenures/' . $existing_id);
        $this->apiAs($this->dev_user, 'GET', '/api/property/property-tenures/-1', [], [])->assertStatus(404);
    }

    public function testCanUsersCreateTenures()
    {
        $this->apiAs($this->dev_user, 'POST', '/api/property/property-tenures/', [
            'name' => 'GottaMakeItImpossibleToCollideAmirite'
        ], [])->assertSuccessful();

        $this->assertInstanceOf(
            PropertyTenure::class,
            PropertyTenure::where('name', 'GottaMakeItImpossibleToCollideAmirite')->first()
        );
    }

    public function testCanUsersUpdateTenures()
    {
        // Select a random record
        $unlucky_record = PropertyTenure::randomRow();
        $before_changes = clone $unlucky_record;

        // Make a patch request
        $this->apiAs($this->dev_user, 'PATCH', '/api/property/property-tenures/' . $unlucky_record->id, [
            'name' => 'ThisIsAPrefix' . $unlucky_record->name,
        ], [])->assertSuccessful();

        // Check if changes happened
        $unlucky_record = $unlucky_record->fresh();
        $this->assertEquals($unlucky_record->name, 'ThisIsAPrefix' . $before_changes->name);
    }

    public function testDoesTenureControllerValidateProperly()
    {
        // Empty name will fail
        $this->apiAs($this->dev_user, 'POST', '/api/property/property-tenures/', [
            'name' => '',
        ], [])->assertJsonValidationErrors([
            'name'
        ]);

        // Proper name will succeed
        $this->apiAs($this->dev_user, 'POST', '/api/property/property-tenures/', [
            'name' => 'ThisWillDefinitelyBeADuplicate',
        ], [])->assertSuccessful();

        // Same name as before will fail as a duplicate
        $this->apiAs($this->dev_user, 'POST', '/api/property/property-tenures/', [
            'name' => 'ThisWillDefinitelyBeADuplicate',
        ], [])->assertJsonValidationErrors([
            'name'
        ]);
    }

    public function testCanUsersDeleteTenures()
    {
        $unlucky_record = (factory(PropertyTenure::class, 1)->create())->first();
        $this->assertInstanceOf(PropertyTenure::class, $unlucky_record);
        $this->apiAs(
            $this->dev_user,
            'DELETE',
            '/api/property/property-tenures/' . $unlucky_record->id,
            [],
            []
        )->assertSuccessful();
        $this->assertNull(PropertyTenure::find($unlucky_record->id));
    }
}
