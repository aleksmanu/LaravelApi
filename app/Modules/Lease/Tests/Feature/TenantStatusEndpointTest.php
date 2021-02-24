<?php
namespace App\Modules\Lease\Tests\Feature;

use App\Modules\Common\Classes\EndpointTest;
use App\Modules\Common\Traits\TestsEndpoints;
use App\Modules\Lease\Models\TenantStatus;
use Tests\TestCase;

class TenantStatusEndpointTest extends EndpointTest
{
    public function testCanUsersIndexTenantStatuses()
    {
        $this->assertGetCountForAllUserTypes('/api/lease/tenant-statuses/', TenantStatus::query());
    }

    public function testCanUsersReadTenantStatuses()
    {
        $existing_id = TenantStatus::first()->id;
        $this->assertGetForAllUserTypes('/api/lease/tenant-statuses/' . $existing_id);
        $this->apiAs($this->dev_user, 'GET', '/api/lease/tenant-statuses/-1', [], [])->assertStatus(404);
    }

    public function testCanUsersCreateTenantStatuses()
    {
        $this->apiAs($this->dev_user, 'POST', '/api/lease/tenant-statuses/', [
            'name' => 'GottaMakeItImpossibleToCollideAmirite'
        ], [])->assertSuccessful();

        $this->assertInstanceOf(
            TenantStatus::class,
            TenantStatus::where('name', 'GottaMakeItImpossibleToCollideAmirite')->first()
        );
    }

    public function testCanUsersUpdateTenantStatuses()
    {
        // Select a random record
        $unlucky_record = TenantStatus::randomRow();
        $before_changes = clone $unlucky_record;

        // Make a patch request
        $this->apiAs($this->dev_user, 'PATCH', '/api/lease/tenant-statuses/' . $unlucky_record->id, [
            'name' => 'ThisIsAPrefix' . $unlucky_record->name,
        ], [])->assertSuccessful();

        // Check if changes happened
        $unlucky_record = $unlucky_record->fresh();
        $this->assertEquals($unlucky_record->name, 'ThisIsAPrefix' . $before_changes->name);
    }

    public function testDoesTenantStatusesControllerValidateProperly()
    {
        // Empty name will fail
        $this->apiAs($this->dev_user, 'POST', '/api/lease/tenant-statuses/', [
            'name' => '',
        ], [])->assertJsonValidationErrors([
            'name'
        ]);

        // Proper name will succeed
        $this->apiAs($this->dev_user, 'POST', '/api/lease/tenant-statuses/', [
            'name' => 'ThisWillDefinitelyBeADuplicate',
        ], [])->assertSuccessful();

        // Same name as before will fail as a duplicate
        $this->apiAs($this->dev_user, 'POST', '/api/lease/tenant-statuses/', [
            'name' => 'ThisWillDefinitelyBeADuplicate',
        ], [])->assertJsonValidationErrors([
            'name'
        ]);
    }

    public function testCanUsersDeleteTenantStatuses()
    {
        $unlucky_record = (factory(TenantStatus::class, 1)->create())->first();
        $this->assertInstanceOf(TenantStatus::class, $unlucky_record);
        $this->apiAs(
            $this->dev_user,
            'DELETE',
            '/api/lease/tenant-statuses/' . $unlucky_record->id,
            [],
            []
        )->assertSuccessful();
        $this->assertNull(TenantStatus::find($unlucky_record->id));
    }
}
