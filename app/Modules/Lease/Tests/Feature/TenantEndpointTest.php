<?php
/**
 * Created by PhpStorm.
 * User: aron
 * Date: 9/4/18
 * Time: 3:44 PM
 */

namespace App\Modules\Lease\Tests\Feature;

use App\Modules\Common\Classes\EndpointTest;
use App\Modules\Common\Traits\TestsCardSummaryData;
use App\Modules\Common\Traits\TestsEndpoints;
use App\Modules\Lease\Models\Lease;
use App\Modules\Lease\Models\Tenant;
use App\Modules\Lease\Models\TenantStatus;
use App\Modules\Property\Models\Property;
use App\Modules\Property\Models\PropertyManager;
use App\Modules\Property\Models\Unit;
use Tests\TestCase;

class TenantEndpointTest extends EndpointTest
{
    use TestsCardSummaryData;

    public function testCanUsersIndexTenants()
    {
        $this->assertGetCountForAllUserTypes('/api/lease/tenants/', Tenant::query());
    }

    public function testCanUsersReadTenants()
    {
        $existing_id = Tenant::first()->id;
        $this->assertGetForAllUserTypes('/api/lease/tenants/' . $existing_id);
        $this->apiAs($this->authoriser_user, 'GET', '/api/lease/tenants/-1', [], [])->assertStatus(404);
    }

    public function testCanUsersCreateTenants()
    {
        $this->apiAs($this->authoriser_user, 'POST', '/api/lease/tenants/', [
            'tenant_status_id'     => TenantStatus::randomRow()->id,
            'lease_id'             => Lease::randomRow()->id,
            'name'                 => 'GottaMakeItImpossibleToCollideAmirite',
            'yardi_tenant_ref'     => 'asd',
            'yardi_tenant_alt_ref' => 'asdf',
        ], [])->assertSuccessful();

        $this->assertInstanceOf(Tenant::class, Tenant::where('name', 'GottaMakeItImpossibleToCollideAmirite')->first());
    }

    public function testCanUsersUpdateTenants()
    {
        // Select a random record
        $unlucky_record = Tenant::randomRow();
        $before_changes = clone $unlucky_record;

        $new_unit = factory(Unit::class, 1)->create([
            'property_id'         => Property::randomRow()->id,
            'property_manager_id' => PropertyManager::randomRow()->id
        ])->first();
        $new_tenant_status_id = factory(TenantStatus::class, 1)->create()->first();
        $new_lease = factory(Lease::class, 1)->create(['unit_id' => $new_unit->id])->first();

        $data = [
            'tenant_status_id'     => $new_tenant_status_id->id,
            'lease_id'             => $new_lease->id,
            'name'                 => 'ThisIsAPrefix' . $unlucky_record->name,
            'yardi_tenant_ref'     => '$' . $unlucky_record->yardi_tenant_ref,
            'yardi_tenant_alt_ref' => '$' . $unlucky_record->yardi_tenant_alt_ref,
            'edit'                 => false
        ];

        // Make a patch request
        $this->apiAs(
            $this->authoriser_user,
            'PATCH',
            '/api/lease/tenants/' . $unlucky_record->id,
            $data,
            []
        )->assertSuccessful();
        $this->assertUpdate($unlucky_record, $new_tenant_status_id, $new_lease, $before_changes);

        $data['edit'] = true;

        $this->apiAs(
            $this->authoriser_user,
            'PATCH',
            '/api/lease/tenants/' . $unlucky_record->id,
            $data,
            []
        )->assertSuccessful();
        $this->assertUpdate($unlucky_record, $new_tenant_status_id, $new_lease, $before_changes);
    }

    private function assertUpdate($unlucky_record, $new_tenant_status_id, $new_lease, $before_changes)
    {
        // Check if changes happened
        $unlucky_record = $unlucky_record->fresh();
        $this->assertEquals($unlucky_record->tenant_status_id, $new_tenant_status_id->id);
        $this->assertEquals($unlucky_record->lease_id, $new_lease->id);
        $this->assertEquals($unlucky_record->name, 'ThisIsAPrefix' . $before_changes->name);
        $this->assertEquals($unlucky_record->yardi_tenant_ref, '$' . $before_changes->yardi_tenant_ref);
        $this->assertEquals($unlucky_record->yardi_tenant_alt_ref, '$' . $before_changes->yardi_tenant_alt_ref);
    }

    public function testDoesTenantsControllerValidateProperly()
    {
        $submitted_data = [
            'tenant_status_id'     => -1,
            'lease_id'             => -1,
            'name'                 => '',
            'yardi_tenant_ref'     => '',
            'yardi_tenant_alt_ref' => '',
        ];

        // Check if all errors are present
        $this->apiAs(
            $this->authoriser_user,
            'POST',
            '/api/lease/tenants/',
            $submitted_data,
            []
        )->assertJsonValidationErrors(array_keys($submitted_data));
    }

    public function testCanUsersDeleteTenants()
    {
        $unlucky_record = (factory(Tenant::class, 1)->create([
            'lease_id' => Lease::first()->id
        ]))->first();
        $this->assertInstanceOf(Tenant::class, $unlucky_record);
        $this->apiAs(
            $this->dev_user,
            'DELETE',
            '/api/lease/tenants/' . $unlucky_record->id,
            [],
            []
        )->assertSuccessful();
        $this->assertNull(Tenant::find($unlucky_record->id));
    }
}
