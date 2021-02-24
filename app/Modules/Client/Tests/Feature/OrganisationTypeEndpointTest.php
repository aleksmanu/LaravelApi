<?php
namespace App\Modules\Client\Tests\Feature;

use App\Modules\Client\Models\OrganisationType;
use App\Modules\Common\Classes\EndpointTest;

class OrganisationTypeEndpointTest extends EndpointTest
{
    public function testCanUsersIndexOrganisationTypes()
    {
        $this->assertGetCountForAllUserTypes('/api/client/organisation-types/', OrganisationType::query());
    }

    public function testCanUsersReadOrganisationTypes()
    {
        $existing_id = OrganisationType::first()->id;
        $this->assertGetForAllUserTypes('/api/client/organisation-types/' . $existing_id);
        $this->apiAs($this->dev_user, 'GET', '/api/client/organisation-types/-1', [], [])->assertStatus(404);
    }

    public function testDoesOrganisationTypecontrollerValidateProperly()
    {
        $this->apiAs($this->dev_user, 'POST', '/api/client/organisation-types/', [
            'name' => ''
        ], [])->assertJsonValidationErrors([
            'name'
        ]);
    }

    public function testCanUsersUpdateOrganisationTypes()
    {
        //Store the original for subsequent comparison
        $someType = OrganisationType::first();
        $original = clone $someType;

        $this->apiAs($this->dev_user, 'PATCH', '/api/client/organisation-types/' . $someType->id, [
            'id'   => $someType->id,
            'name' => $someType->name . 'CHANGED',
        ], [])->assertSuccessful();

        //Reload the model to catch database changes
        $someType = $someType->fresh();

        //Check differences are as expected
        $this->assertEquals($someType->name, $original->name . "CHANGED");
    }

    public function testCanUsersDeleteOrganisationTypes()
    {
        $unlucky_record = (factory(OrganisationType::class, 1)->create())->first();
        $this->assertInstanceOf(OrganisationType::class, $unlucky_record);
        $this->apiAs(
            $this->dev_user,
            'DELETE',
            '/api/client/organisation-types/' . $unlucky_record->id,
            [],
            []
        )->assertSuccessful();
        $this->assertNull(OrganisationType::find($unlucky_record->id));
    }
}
