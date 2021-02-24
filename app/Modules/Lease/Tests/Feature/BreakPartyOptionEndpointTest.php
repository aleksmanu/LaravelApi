<?php
namespace App\Modules\Lease\Tests\Feature;

use App\Modules\Common\Classes\EndpointTest;
use App\Modules\Lease\Models\BreakPartyOption;

class BreakPartyOptionEndpointTest extends EndpointTest
{
    public function testCanUsersIndexBreakPartyOptions()
    {
        $this->assertGetCountForAllUserTypes('/api/lease/break-party-options/', BreakPartyOption::query());
    }

    public function testCanUsersReadBreakPartyOptions()
    {
        $existing_id = BreakPartyOption::first()->id;
        $this->assertGetForAllUserTypes('/api/lease/break-party-options/' . $existing_id);
        $this->apiAs($this->dev_user, 'GET', '/api/lease/break-party-options/-1', [], [])->assertStatus(404);
    }

    public function testCanUsersCreateBreakPartyOptions()
    {
        $this->apiAs($this->dev_user, 'POST', '/api/lease/break-party-options/', [
            'name' => 'GottaMakeItImpossibleToCollideAmirite',
        ], [])->assertSuccessful();

        $this->assertInstanceOf(
            BreakPartyOption::class,
            BreakPartyOption::where('name', 'GottaMakeItImpossibleToCollideAmirite')->first()
        );
    }

    public function testCanUsersUpdateBreakPartyOptions()
    {
        // Select a random record
        $unlucky_record = BreakPartyOption::randomRow();
        $before_changes = clone $unlucky_record;

        // Make a patch request
        $this->apiAs($this->dev_user, 'PATCH', '/api/lease/break-party-options/' . $unlucky_record->id, [
            'name' => 'ThisIsAPrefix' . $unlucky_record->name,
        ], [])->assertSuccessful();

        // Check if changes happened
        $unlucky_record = $unlucky_record->fresh();
        $this->assertEquals($unlucky_record->name, 'ThisIsAPrefix' . $before_changes->name);
    }

    public function testDoesBreakPartyOptionsControllerValidateProperly()
    {
        // Empty name will fail
        $this->apiAs($this->dev_user, 'POST', '/api/lease/break-party-options/', [
            'name' => '',
        ], [])->assertJsonValidationErrors([
            'name'
        ]);

        // Proper name will succeed
        $this->apiAs($this->dev_user, 'POST', '/api/lease/break-party-options/', [
            'name' => 'ThisWillDefinitelyBeADuplicate',
        ], [])->assertSuccessful();

        // Same name as before will fail as a duplicate
        $this->apiAs($this->dev_user, 'POST', '/api/lease/break-party-options/', [
            'name' => 'ThisWillDefinitelyBeADuplicate',
        ], [])->assertJsonValidationErrors([
            'name'
        ]);
    }

    public function testCanUsersDeleteBreakPartyOptions()
    {
        $unlucky_record = (factory(BreakPartyOption::class, 1)->create())->first();
        $this->assertInstanceOf(BreakPartyOption::class, $unlucky_record);
        $this->apiAs(
            $this->dev_user,
            'DELETE',
            '/api/lease/break-party-options/' . $unlucky_record->id,
            [],
            []
        )->assertSuccessful();
        $this->assertNull(BreakPartyOption::find($unlucky_record->id));
    }
}
