<?php
namespace App\Modules\Common\Tests\Feature;

use App\Modules\Common\Classes\EndpointTest;
use App\Modules\Common\Models\Country;

class CountryEndpointTest extends EndpointTest
{
    public function testCanUsersIndexCountries()
    {
        $this->assertGetCountForAllUserTypes('/api/common/countries/', Country::query());
    }

    public function testCanUsersReadCountries()
    {
        $existing_id = Country::first()->id;
        $this->assertGetForAllUserTypes('/api/common/countries/' . $existing_id);
        $this->apiAs($this->dev_user, 'GET', '/api/common/countries/-1', [], [])->assertStatus(404);
    }

    public function testCanUsersCreateCountries()
    {
        $this->apiAs($this->dev_user, 'POST', '/api/common/countries/', [
            'name' => 'GottaMakeItImpossibleToCollideAmirite',
        ], [])->assertSuccessful();

        $this->assertInstanceOf(
            Country::class,
            Country::where('name', 'GottaMakeItImpossibleToCollideAmirite')->first()
        );
    }

    public function testCanUsersUpdateCountries()
    {
        // Select a random record
        $unlucky_record = Country::randomRow();
        $before_changes = clone $unlucky_record;

        // Make a patch request
        $this->apiAs($this->dev_user, 'PATCH', '/api/common/countries/' . $unlucky_record->id, [
            'name' => 'ThisIsAPrefix' . $unlucky_record->name,
        ], [])->assertSuccessful();

        // Check if changes happened
        $unlucky_record = $unlucky_record->fresh();
        $this->assertEquals($unlucky_record->name, 'ThisIsAPrefix' . $before_changes->name);
    }

    public function testDoesCountriesControllerValidateProperly()
    {
        // Empty name will fail
        $this->apiAs($this->dev_user, 'POST', '/api/common/countries/', [
            'name' => '',
        ], [])->assertJsonValidationErrors([
            'name'
        ]);
    }

    public function testCanUsersDeleteCountries()
    {
        $unlucky_record = (factory(Country::class, 1)->create())->first();
        $this->assertInstanceOf(Country::class, $unlucky_record);
        $this->apiAs(
            $this->dev_user,
            'DELETE',
            '/api/common/countries/' . $unlucky_record->id,
            [],
            []
        )->assertSuccessful();
        $this->assertNull(Country::find($unlucky_record->id));
    }
}
