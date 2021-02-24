<?php
namespace App\Modules\Lease\Tests\Feature;

use App\Modules\Common\Classes\EndpointTest;
use App\Modules\Lease\Models\TransactionType;

class TransactionTypeEndpointTest extends EndpointTest
{
    public function testCanUsersIndexTransactionTypes()
    {
        $this->assertGetCountForAllUserTypes('/api/lease/transaction-types/', TransactionType::query());
    }

    public function testCanUsersReadTransactionTypes()
    {
        $existing_id = TransactionType::first()->id;
        $this->assertGetForAllUserTypes('/api/lease/transaction-types/' . $existing_id);
        $this->apiAs($this->dev_user, 'GET', '/api/lease/transaction-types/-1', [], [])->assertStatus(404);
    }

    public function testCanUsersCreateTransactionTypes()
    {
        $this->apiAs($this->dev_user, 'POST', '/api/lease/transaction-types/', [
            'name' => 'GottaMakeItImpossibleToCollideAmirite',
            'code' => '123',
        ], [])->assertSuccessful();

        $this->assertInstanceOf(
            TransactionType::class,
            TransactionType::where('name', 'GottaMakeItImpossibleToCollideAmirite')->first()
        );
    }

    public function testCanUsersUpdateTransactionTypes()
    {
        // Select a random record
        $unlucky_record = TransactionType::randomRow();
        $before_changes = clone $unlucky_record;

        // Make a patch request
        $this->apiAs($this->dev_user, 'PATCH', '/api/lease/transaction-types/' . $unlucky_record->id, [
            'name' => 'ThisIsAPrefix' . $unlucky_record->name,
            'code' => '123'
        ], [])->assertSuccessful();

        // Check if changes happened
        $unlucky_record = $unlucky_record->fresh();
        $this->assertEquals($unlucky_record->name, 'ThisIsAPrefix' . $before_changes->name);
        $this->assertEquals($unlucky_record->code, '123');
    }

    public function testDoesTransactionTypesControllerValidateProperly()
    {
        // Empty name will fail
        $this->apiAs($this->dev_user, 'POST', '/api/lease/transaction-types/', [
            'name' => '',
            'code' => '',
        ], [])->assertJsonValidationErrors([
            'name', 'code',
        ]);

        // Proper name will succeed
        $this->apiAs($this->dev_user, 'POST', '/api/lease/transaction-types/', [
            'name' => 'ThisWillDefinitelyBeADuplicate',
            'code' => '1236969',
        ], [])->assertSuccessful();

        // Same name as before will fail as a duplicate
        $this->apiAs($this->dev_user, 'POST', '/api/lease/transaction-types/', [
            'name' => 'ThisWillDefinitelyBeADuplicate',
            'code' => '1236969',
        ], [])->assertJsonValidationErrors([
            'name', 'code'
        ]);
    }

    public function testVanUsersDeleteTransactionTypes()
    {
        $unlucky_record = TransactionType::create(['name' => 'test_imperial_toothwidths', 'code' => '123']);
        $this->assertInstanceOf(TransactionType::class, $unlucky_record);
        $this->apiAs(
            $this->dev_user,
            'DELETE',
            '/api/lease/transaction-types/' . $unlucky_record->id,
            [],
            []
        )->assertSuccessful();
        $this->assertNull(TransactionType::find($unlucky_record->id));
    }
}
