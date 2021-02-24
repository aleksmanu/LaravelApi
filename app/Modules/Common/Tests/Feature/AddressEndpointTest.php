<?php
namespace App\Modules\Common\Tests\Feature;

use App\Modules\Common\Classes\EndpointTest;
use App\Modules\Common\Models\Address;
use App\Modules\Common\Models\Country;
use App\Modules\Common\Models\County;

class AddressEndpointTest extends EndpointTest
{

    public function testCanUsersIndexAddresses()
    {
        $this->assertGetCountForAllUserTypes('/api/common/addresses/', Address::query());
    }

    public function testCanUsersReadAddresses()
    {
        $existing_id = Address::first()->id;
        $this->assertGetForAllUserTypes('/api/common/addresses/' . $existing_id);
        $this->apiAs($this->dev_user, 'GET', '/api/common/addresses/-1', [], [])->assertStatus(404);
    }

    public function testCanUsersCreateAddresses()
    {
        $this->apiAs($this->dev_user, 'POST', '/api/common/addresses/', [
            'county_id'  => County::randomRow()->id,
            'country_id' => Country::randomRow()->id,
            'unit'       => '12a',
            'building'   => 'Some Building',
            'number'     => '5',
            'street'     => 'Grove',
            'estate'     => 'Momma`s crib',
            'suburb'     => 'Ganton',
            'town'       => 'Los Santos',
            'postcode'   => 'L1 234',
        ], [])->assertSuccessful();

        $this->assertInstanceOf(Address::class, Address::where('estate', 'Momma`s crib')->first());
    }

    public function testCanUsersUpdateAddresses()
    {
        // Select a random record
        $unlucky_record = Address::randomRow();
        $before_changes = clone $unlucky_record;
        $rando_county   = County::randomRow();
        $country        = Country::randomRow();

        // Make a patch request
        $r = $this->apiAs($this->dev_user, 'PATCH', '/api/common/addresses/' . $unlucky_record->id, [
            'county_id'  => $rando_county->id,
            'country_id' => $country->id,
            'unit'       => 'pfx' . $unlucky_record->unit,
            'building'   => 'pfx' . $unlucky_record->building,
            'number'     => substr($unlucky_record->number, 0, -1),
            'street'     => 'pfx' . $unlucky_record->street,
            'estate'     => 'pfx' . $unlucky_record->estate,
            'suburb'     => 'pfx' . $unlucky_record->suburb,
            'town'       => 'pfx' . $unlucky_record->town,
            'postcode'   => substr($unlucky_record->postcode, 0, -1),
        ], [])->assertSuccessful();

        // Check if changes happened
        $unlucky_record = $unlucky_record->fresh();
        $this->assertEquals($unlucky_record->county_id, $rando_county->id);
        $this->assertEquals($unlucky_record->country_id, $country->id);
        $this->assertEquals($unlucky_record->unit, 'pfx' . $before_changes->unit);
        $this->assertEquals($unlucky_record->building, 'pfx' . $before_changes->building);
        $this->assertEquals($unlucky_record->number, substr($before_changes->number, 0, -1));
        $this->assertEquals($unlucky_record->street, 'pfx' . $before_changes->street);
        $this->assertEquals($unlucky_record->estate, 'pfx' . $before_changes->estate);
        $this->assertEquals($unlucky_record->suburb, 'pfx' . $before_changes->suburb);
        $this->assertEquals($unlucky_record->town, 'pfx' . $before_changes->town);
        $this->assertEquals($unlucky_record->postcode, substr($before_changes->postcode, 0, -1));
    }

    public function testDoesAddressesControllerValidateProperly()
    {
        // Empty name will fail
        $this->apiAs($this->dev_user, 'POST', '/api/common/addresses/', [
            'county_id'  => -1,
            'country_id' => -1,
        ], [])->assertJsonValidationErrors([
                                               'county_id', 'country_id'
                                           ]);
    }

    public function testCanUsersDeleteAddresses()
    {
        $unlucky_record = (factory(Address::class, 1)->create())->first();
        $this->assertInstanceOf(Address::class, $unlucky_record);
        $this->apiAs(
            $this->dev_user,
            'DELETE',
            '/api/common/addresses/' . $unlucky_record->id,
            [],
            []
        )->assertSuccessful();
        $this->assertNull(Address::find($unlucky_record->id));
    }
}
