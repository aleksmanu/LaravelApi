<?php
namespace App\Modules\Lease\Tests\Feature;

use App\Modules\Common\Classes\EndpointTest;
use App\Modules\Common\Traits\TestsEndpoints;
use App\Modules\Lease\Models\LeaseType;
use Tests\TestCase;

class LeaseTypeEndpointTest extends EndpointTest
{
    public function testCanUsersIndexLeaseType()
    {
        $this->assertGetCountForAllUserTypes('/api/lease/lease-types/', LeaseType::query());
    }

    public function testCanUsersReadLeaseType()
    {
        $existing_id = LeaseType::first()->id;
        $this->assertGetForAllUserTypes('/api/lease/lease-types/' . $existing_id);
        $this->apiAs($this->dev_user, 'GET', '/api/lease/lease-types/-1', [], [])->assertStatus(404);
    }

    public function testCanUsersCreateLeaseType()
    {
        $this->apiAs($this->dev_user, 'POST', '/api/lease/lease-types/', [
            'name' => 'GottaMakeItImpossibleToCollideAmirite',
        ], [])->assertSuccessful();

        $this->assertInstanceOf(
            LeaseType::class,
            LeaseType::where('name', 'GottaMakeItImpossibleToCollideAmirite')->first()
        );
    }

    public function testCanUsersUpdateLeaseType()
    {
        // Select a random record
        $unlucky_record = LeaseType::randomRow();
        $before_changes = clone $unlucky_record;

        // Make a patch request
        $this->apiAs($this->dev_user, 'PATCH', '/api/lease/lease-types/' . $unlucky_record->id, [
            'name' => 'ThisIsAPrefix' . $unlucky_record->name,
            // Careful not to overflow max len, let's shorten it instead of the usual prefix/suffix
        ], [])->assertSuccessful();

        // Check if changes happened
        $unlucky_record = $unlucky_record->fresh();
        $this->assertEquals($unlucky_record->name, 'ThisIsAPrefix' . $before_changes->name);
        $this->assertEquals($unlucky_record->code, substr($before_changes->code, 0, -1));
    }

    public function testDoesLeaseTypeControllerValidateProperly()
    {
        // Empty name will fail
        $this->apiAs($this->dev_user, 'POST', '/api/lease/lease-types/', [
            'name' => '',
        ], [])->assertJsonValidationErrors([
            'name'
        ]);

        // Proper name will succeed
        $this->apiAs($this->dev_user, 'POST', '/api/lease/lease-types/', [
            'name' => 'ThisWillDefinitelyBeADuplicate',
        ], [])->assertSuccessful();

        // Same name as before will fail as a duplicate
        $this->apiAs($this->dev_user, 'POST', '/api/lease/lease-types/', [
            'name' => 'ThisWillDefinitelyBeADuplicate',
        ], [])->assertJsonValidationErrors([
            'name'
        ]);
    }

    public function testCanUsersDeleteLeaseType()
    {
        $unlucky_record = (factory(LeaseType::class, 1)->create())->first();
        $this->assertInstanceOf(LeaseType::class, $unlucky_record);
        $this->apiAs(
            $this->dev_user,
            'DELETE',
            '/api/lease/lease-types/' . $unlucky_record->id,
            [],
            []
        )->assertSuccessful();
        $this->assertNull(LeaseType::find($unlucky_record->id));
    }
}
