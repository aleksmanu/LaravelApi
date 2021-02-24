<?php
namespace App\Modules\Lease\Tests\Feature;

use App\Modules\Common\Classes\EndpointTest;
use App\Modules\Lease\Models\PaidStatus;

class PaidStatusEndpointTest extends EndpointTest
{
    public function testCanUsersIndexPaidStatuses()
    {
        $this->assertGetCountForAllUserTypes('/api/lease/paid-statuses/', PaidStatus::query());
    }

    public function testCanUsersReadPaidStatuses()
    {
        $existing_id = PaidStatus::first()->id;
        $this->assertGetForAllUserTypes('/api/lease/paid-statuses/' . $existing_id);
        $this->apiAs($this->dev_user, 'GET', '/api/lease/paid-statuses/-1', [], [])->assertStatus(404);
    }

    public function testCanUsersCreatePaidStatuses()
    {
        $this->apiAs($this->dev_user, 'POST', '/api/lease/paid-statuses/', [
            'name' => 'GottaMakeItImpossibleToCollideAmirite',
        ], [])->assertSuccessful();

        $this->assertInstanceOf(
            PaidStatus::class,
            PaidStatus::where('name', 'GottaMakeItImpossibleToCollideAmirite')->first()
        );
    }

    public function testCanUsersUpdatePaidStatuses()
    {
        // Select a random record
        $unlucky_record = PaidStatus::randomRow();
        $before_changes = clone $unlucky_record;

        // Make a patch request
        $this->apiAs($this->dev_user, 'PATCH', '/api/lease/paid-statuses/' . $unlucky_record->id, [
            'name' => 'ThisIsAPrefix' . $unlucky_record->name,
        ], [])->assertSuccessful();

        // Check if changes happened
        $unlucky_record = $unlucky_record->fresh();
        $this->assertEquals($unlucky_record->name, 'ThisIsAPrefix' . $before_changes->name);
    }

    public function testDoesPaidStatusesControllerValidateProperly()
    {
        // Empty name will fail
        $this->apiAs($this->dev_user, 'POST', '/api/lease/paid-statuses/', [
            'name' => '',
        ], [])->assertJsonValidationErrors([
            'name',
        ]);

        // Proper name will succeed
        $this->apiAs($this->dev_user, 'POST', '/api/lease/paid-statuses/', [
            'name' => 'ThisWillDefinitelyBeADuplicate',
        ], [])->assertSuccessful();

        // Same name as before will fail as a duplicate
        $this->apiAs($this->dev_user, 'POST', '/api/lease/paid-statuses/', [
            'name' => 'ThisWillDefinitelyBeADuplicate',
        ], [])->assertJsonValidationErrors([
            'name'
        ]);
    }

    public function testCanUsersDeletePaidStatuses()
    {
        $unlucky_record = PaidStatus::create(['name' => 'test_imperial_toothwidths', 'slug' => 'slimy-and-not-salty']);
        $this->assertInstanceOf(PaidStatus::class, $unlucky_record);
        $this->apiAs(
            $this->dev_user,
            'DELETE',
            '/api/lease/paid-statuses/' . $unlucky_record->id,
            [],
            []
        )->assertSuccessful();
        $this->assertNull(PaidStatus::find($unlucky_record->id));
    }
}
