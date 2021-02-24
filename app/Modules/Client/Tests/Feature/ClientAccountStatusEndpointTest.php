<?php
namespace App\Modules\Client\Tests\Feature;

use App\Modules\Client\Models\ClientAccountStatus;
use App\Modules\Common\Classes\EndpointTest;

class ClientAccountStatusEndpointTest extends EndpointTest
{
    public function testCanUsersIndexClientAccountStatuses()
    {
        $this->assertGetCountForAllUserTypes('/api/client/client-account-statuses/', ClientAccountStatus::query());
    }

    public function testCanUsersReadClientAccountStatuses()
    {
        $existing_id = ClientAccountStatus::first()->id;
        $this->assertGetForAllUserTypes('/api/client/client-account-statuses/' . $existing_id);
        $this->apiAs($this->dev_user, 'GET', '/api/client/client-account-statuses/-1', [], [])->assertStatus(404);
    }

    public function testCanUsersCreateClientAccountStatuses()
    {
        $this->apiAs($this->dev_user, 'POST', '/api/client/client-account-statuses/', [
            'name' => 'GottaMakeItImpossibleToCollideAmirite'
        ], [])->assertSuccessful();

        $this->assertInstanceOf(
            ClientAccountStatus::class,
            ClientAccountStatus::where('name', 'GottaMakeItImpossibleToCollideAmirite')->first()
        );
    }

    public function testCanUsersUpdateClientAccountStatuses()
    {
        // Select a random record
        $unlucky_record = ClientAccountStatus::randomRow();
        $before_changes = clone $unlucky_record;

        // Make a patch request
        $this->apiAs($this->dev_user, 'PATCH', '/api/client/client-account-statuses/' . $unlucky_record->id, [
            'name' => 'ThisIsAPrefix' . $unlucky_record->name,
        ], [])->assertSuccessful();

        // Check if changes happened
        $unlucky_record = $unlucky_record->fresh();
        $this->assertEquals($unlucky_record->name, 'ThisIsAPrefix' . $before_changes->name);
    }

    public function testDoesClientAccountControllerValidateProperly()
    {
        // Empty name will fail
        $this->apiAs($this->dev_user, 'POST', '/api/client/client-account-statuses/', [
            'name' => '',
        ], [])->assertJsonValidationErrors([
            'name'
        ]);

        // Proper name will succeed
        $this->apiAs($this->dev_user, 'POST', '/api/client/client-account-statuses/', [
            'name' => 'ThisWillDefinitelyBeADuplicate',
        ], [])->assertSuccessful();

        // Same name as before will fail as a duplicate
        $this->apiAs($this->dev_user, 'POST', '/api/client/client-account-statuses/', [
            'name' => 'ThisWillDefinitelyBeADuplicate',
        ], [])->assertJsonValidationErrors([
            'name'
        ]);
    }

    public function testCanUsersDeleteClientAccountStatuses()
    {
        $unlucky_record = (factory(ClientAccountStatus::class, 1)->create())->first();
        $this->assertInstanceOf(ClientAccountStatus::class, $unlucky_record);
        $this->apiAs(
            $this->dev_user,
            'DELETE',
            '/api/client/client-account-statuses/' . $unlucky_record->id,
            [],
            []
        )->assertSuccessful();
        $this->assertNull(ClientAccountStatus::find($unlucky_record->id));
    }
}
