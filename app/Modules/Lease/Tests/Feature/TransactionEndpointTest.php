<?php
namespace App\Modules\Lease\Tests\Feature;

use App\Modules\Common\Classes\EndpointTest;
use App\Modules\Lease\Models\Lease;
use App\Modules\Lease\Models\PaidStatus;
use App\Modules\Lease\Models\Transaction;
use App\Modules\Lease\Models\TransactionType;
use App\Modules\Property\Models\Unit;

class TransactionEndpointTest extends EndpointTest
{
    public function testCanUsersIndexTransactions()
    {
        $this->assertGetCountForAllUserTypes('/api/lease/transactions/', Transaction::query());
    }

    public function testCanUsersReadTransactions()
    {
        $existing_id = Transaction::randomRow()->id;
        $this->assertGetForAllUserTypes('/api/lease/transactions/' . $existing_id);
        $this->apiAs($this->dev_user, 'GET', '/api/client/portfolios/-1', [], [])->assertStatus(404);
    }

    public function testCanUsersCreateTransactions()
    {
        $seedTransaction = factory(Transaction::class, 1)->make(['lease_id' => Lease::randomRow()->id])->first();
        $this->apiAs(
            $this->dev_user,
            'POST',
            '/api/lease/transactions/',
            $seedTransaction->toArray(),
            []
        )->assertSuccessful();
        $this->assertInstanceOf(
            Transaction::class,
            Transaction::where('yardi_transaction_ref', $seedTransaction->yardi_transaction_ref)->first()
        );
    }

    public function testCanUsersUpdateTransactions()
    {
        // Select a random record
        $unlucky_record = Transaction::randomRow();
        $before_changes = clone $unlucky_record;

        // Create some models to attach to
        $new_paid_status = factory(PaidStatus::class, 1)->create()->first();
        $new_lease = factory(Lease::class, 1)->create(['unit_id' => Unit::randomRow()->id])->first();
        $new_transaction_type = factory(TransactionType::class, 1)->create()->first();

        $data = [
            'lease_charge_type_id' => $new_transaction_type->id,
            'paid_status_id' => $new_paid_status->id,
            'invoice_number' => $before_changes->invoice_number . 'x',
            'amount' => $before_changes->amount + 1,
            'vat' => $before_changes->vat + 1,
            'gross' => $before_changes->gross + 1,
            'gross_received' => $before_changes->gross_received + 1,
            'yardi_transaction_ref' => $before_changes->yardi_transaction_ref + 1,
            'lease_id' => $new_lease->id,
        ];

        // Make a patch request
        $this->apiAs(
            $this->dev_user,
            'PATCH',
            '/api/lease/transactions/' . $unlucky_record->id,
            $data,
            []
        )->assertSuccessful();

        // Check if changes happened
        $unlucky_record = $unlucky_record->fresh();
        $this->assertEquals($unlucky_record->invoice_number, $before_changes->invoice_number . 'x');
        $this->assertEquals($unlucky_record->amount, $before_changes->amount + 1);
        $this->assertEquals($unlucky_record->vat, $before_changes->vat + 1);
        $this->assertEquals($unlucky_record->gross, $before_changes->gross + 1);
        $this->assertEquals($unlucky_record->gross_received, $before_changes->gross_received + 1);
        $this->assertEquals($unlucky_record->yardi_transaction_ref, $before_changes->yardi_transaction_ref + 1);
        $this->assertEquals($unlucky_record->transaction_type_id, $new_transaction_type->id);
        $this->assertEquals($unlucky_record->paid_status_id, $new_paid_status->id);
        $this->assertEquals($unlucky_record->lease_id, $new_lease->id);
    }

    public function testDoesTransactionsControllerValidateProperly()
    {
        $postData = [
            'paid_status_id' => -1,
            'lease_charge_type_id' => -1,
            'lease_id' => -1,
        ];

        $this->apiAs(
            $this->dev_user,
            'POST',
            '/api/lease/transactions/',
            $postData,
            []
        )->assertJsonValidationErrors(array_keys($postData));
    }

    public function testCanUsersDeleteTransactions()
    {
        $unlucky_record = (factory(Transaction::class, 1)->create(['lease_id' => Lease::randomRow()->id]))->first();
        $this->assertInstanceOf(Transaction::class, $unlucky_record);
        $this->apiAs(
            $this->dev_user,
            'DELETE',
            '/api/lease/transactions/' . $unlucky_record->id,
            [],
            []
        )->assertSuccessful();
        $this->assertNull(Transaction::find($unlucky_record->id));
    }

    public function testCanUsersPaginateTransactionsForUnitDataTable()
    {
        // Try with two values to avoid the .00001% chance the size of the full table is the attempted page size
        $result = $this->apiAs($this->dev_user, 'GET', '/api/lease/transactions/unit-data-table', [
            'limit'           => 2,
        ], []);
        $result->assertSuccessful();
        $this->assertSame(2, count($result->json()['rows']));

        $result = $this->apiAs($this->dev_user, 'GET', '/api/lease/transactions/unit-data-table', [
            'limit'           => 3,
        ], []);
        $result->assertSuccessful();
        $this->assertSame(3, count($result->json()['rows']));
    }

    public function testCanUsersSortTransactionsForUnitDataTable()
    {
        // Try with two values to avoid the .00001% chance the size of the full table is the attempted page size
        $result = $this->apiAs($this->dev_user, 'GET', '/api/lease/transactions/unit-data-table', [
            'sort_column' => 'id',
            'sort_order'  => 'desc',
            'limit'       => 1
        ], []);
        $result->assertSuccessful();
        $this->assertSame(Transaction::orderBy('id', 'desc')->first()->id, $result->json()['rows'][0]['id']);

        $result = $this->apiAs($this->dev_user, 'GET', '/api/lease/transactions/unit-data-table', [
            'sort_column' => 'id',
            'sort_order'  => 'asc',
            'limit'       => 1
        ], []);
        $result->assertSuccessful();
        $this->assertSame(Transaction::orderBy('id', 'asc')->first()->id, $result->json()['rows'][0]['id']);
    }

    public function testCanUsersFilterTransactionsForUnitDataTable()
    {
        $filter_unit = Unit::randomRow();
        $leases = $filter_unit->leases->pluck('id');

        // Check them all, why not?
        $total_rows = $filter_unit->transactions()->count();

        $result = $this->apiAs($this->dev_user, 'GET', '/api/lease/transactions/unit-data-table', [
            'limit' => $total_rows,
            'unit_id'           => $filter_unit->id,
        ], []);
        $result->assertSuccessful();
        foreach ($result->json()['rows'] as $item) {
            $this->assertContains($item['lease_id'], $leases);
        }
    }

    public function testCanUsersReadTransactionsDataTable()
    {
        $result = $this->apiAs($this->dev_user, 'GET', '/api/lease/transactions/unit-data-table', [
            'limit' => 1,
        ], []);
        $result->assertSuccessful();

        $result->assertJsonStructure([
            'rows' => [
                '*' => [
                    'amount',
                    'created_at',
                    'due_at',
                    'gross',
                    'gross_received',
                    'id',
                    'invoice_number',
                    'lease_id',
                    'paid_at',
                    'paid_status_id',
                    'period_from',
                    'period_to',
                    'lease_charge_type_id',
                    'updated_at',
                    'vat',
                    'yardi_transaction_ref',
                    'transaction_type' => [
                        'name', 'code'
                    ],
                    'paid_status' => [
                        'name'
                    ]
                ]
            ],
            'row_count'
        ]);
    }
}
