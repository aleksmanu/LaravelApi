<?php
namespace App\Modules\Lease\Tests\Feature;

use App\Modules\Common\Classes\EndpointTest;
use App\Modules\Lease\Models\RentFrequency;

class RentFrequencyEndpointTest extends EndpointTest
{
    public function testCanUsersIndexRentFrequencies()
    {
        $this->assertGetCountForAllUserTypes('/api/lease/rent-frequencies/', RentFrequency::query());
    }

    public function testCanUsersReadRentFrequencies()
    {
        $existing_id = RentFrequency::first()->id;
        $this->assertGetForAllUserTypes('/api/lease/rent-frequencies/' . $existing_id);
        $this->apiAs($this->dev_user, 'GET', '/api/lease/rent-frequencies/-1', [], [])->assertStatus(404);
    }

    public function testCanUsersCreateRentFrequencies()
    {
        $this->apiAs($this->dev_user, 'POST', '/api/lease/rent-frequencies/', [
            'name' => 'GottaMakeItImpossibleToCollideAmirite',
        ], [])->assertSuccessful();

        $this->assertInstanceOf(
            RentFrequency::class,
            RentFrequency::where('name', 'GottaMakeItImpossibleToCollideAmirite')->first()
        );
    }

    public function testCanUsersUpdateRentFrequencies()
    {
        // Select a random record
        $unlucky_record = RentFrequency::randomRow();
        $before_changes = clone $unlucky_record;

        // Make a patch request
        $this->apiAs($this->dev_user, 'PATCH', '/api/lease/rent-frequencies/' . $unlucky_record->id, [
            'name' => 'ThisIsAPrefix' . $unlucky_record->name,
        ], [])->assertSuccessful();

        // Check if changes happened
        $unlucky_record = $unlucky_record->fresh();
        $this->assertEquals($unlucky_record->name, 'ThisIsAPrefix' . $before_changes->name);
    }

    public function testDoesRentFrequenciesControllerValidateProperly()
    {
        // Empty name will fail
        $this->apiAs($this->dev_user, 'POST', '/api/lease/rent-frequencies/', [
            'name' => '',
        ], [])->assertJsonValidationErrors([
            'name'
        ]);

        // Proper name will succeed
        $this->apiAs($this->dev_user, 'POST', '/api/lease/rent-frequencies/', [
            'name' => 'ThisWillDefinitelyBeADuplicate',
        ], [])->assertSuccessful();

        // Same name as before will fail as a duplicate
        $this->apiAs($this->dev_user, 'POST', '/api/lease/rent-frequencies/', [
            'name' => 'ThisWillDefinitelyBeADuplicate',
        ], [])->assertJsonValidationErrors([
            'name'
        ]);
    }

    public function testCanUsersDeleteRentFrequencies()
    {
        $unlucky_record = (factory(RentFrequency::class, 1)->create())->first();
        $this->assertInstanceOf(RentFrequency::class, $unlucky_record);
        $this->apiAs(
            $this->dev_user,
            'DELETE',
            '/api/lease/rent-frequencies/' . $unlucky_record->id,
            [],
            []
        )->assertSuccessful();
        $this->assertNull(RentFrequency::find($unlucky_record->id));
    }
}
