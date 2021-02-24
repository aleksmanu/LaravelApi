<?php
namespace App\Modules\Client\Tests\Feature;

use App\Modules\Client\Models\ClientAccount;
use App\Modules\Client\Models\ClientAccountStatus;
use App\Modules\Client\Models\OrganisationType;
use App\Modules\Common\Classes\EndpointTest;
use App\Modules\Common\Models\Address;
use App\Modules\Common\Traits\TestsCardSummaryData;
use App\Modules\Property\Models\PropertyManager;
use Tests\TestCase;

class ClientAccountEndpointTest extends EndpointTest
{
    use TestsCardSummaryData;

    public function testCanUsersIndexClientAccounts()
    {
        $this->assertGetCountForAllUserTypes('/api/client/client-accounts/', ClientAccount::query());
    }

    public function testCanUsersReadClientAccounts()
    {
        $existing_id = ClientAccount::first()->id;
        $this->assertGetForAllUserTypes('/api/client/client-accounts/' . $existing_id);
        $this->apiAs($this->dev_user, 'GET', '/api/client/portfolios/-1', [], [])->assertStatus(404);
    }

    public function testCanUsersCreateClientAccounts()
    {
        $data = [
            'organisation_type_id'     => OrganisationType::randomRow()->id,
            'property_manager_id'      => PropertyManager::randomRow()->id,
            'client_account_status_id' => ClientAccountStatus::randomRow()->id,
            'name'                     => 'GottaMakeItImpossibleToCollideAmirite',
            'yardi_client_ref'         => 'aezakmi',
            'yardi_alt_ref'            => 'bigbang'
        ] + Address::randomRow()->toAddrArray();

        $this->apiAs($this->dev_user, 'POST', '/api/client/client-accounts/', $data, [])->assertSuccessful();

        $this->assertInstanceOf(
            ClientAccount::class,
            ClientAccount::where('name', 'GottaMakeItImpossibleToCollideAmirite')->first()
        );
    }

    public function testCanUsersUpdateClientAccounts()
    {
        //TODO: Add a test to check that other users can do batch based edits
        // Select a random record
        $unlucky_record = ClientAccount::randomRow();
        $before_changes = clone $unlucky_record;

        // Create some models to attach to
        $new_organisation_type     = factory(OrganisationType::class, 1)->create()->first();
        $new_address               = factory(Address::class, 1)->create()->first();
        $new_property_manager      = factory(PropertyManager::class, 1)->create()->first();
        $new_client_account_status = factory(ClientAccountStatus::class, 1)->create()->first();

        $data = [
            'organisation_type_id'     => $new_organisation_type->id,
            'property_manager_id'      => $new_property_manager->id,
            'client_account_status_id' => $new_client_account_status->id,
            'name'                     => 'ThisIsAPrefix' . $unlucky_record->name,
            'yardi_client_ref'         => 'pfx' . $unlucky_record->yardi_client_ref,
            'yardi_alt_ref'            => 'pfx' . $unlucky_record->yardi_alt_ref,
            'edit'                     => false
        ] + $new_address->toAddrArray();

        // Make a patch request
        $this->apiAs(
            $this->authoriser_user,
            'PATCH',
            '/api/client/client-accounts/' . $unlucky_record->id,
            $data,
            []
        )->assertSuccessful();

        $this->assertUpdate(
            $unlucky_record,
            $before_changes,
            $new_organisation_type,
            $new_property_manager,
            $new_client_account_status,
            $new_address
        );

        $data['edit'] = true;

        // Make a patch request
        $this->apiAs(
            $this->authoriser_user,
            'PATCH',
            '/api/client/client-accounts/' . $unlucky_record->id,
            $data,
            []
        )->assertSuccessful();

        $this->assertUpdate(
            $unlucky_record,
            $before_changes,
            $new_organisation_type,
            $new_property_manager,
            $new_client_account_status,
            $new_address
        );
    }

    private function assertUpdate(
        $unlucky_record,
        $before_changes,
        $new_organisation_type,
        $new_property_manager,
        $new_client_account_status,
        $new_address
    ) {
        // Check if changes happened
        $unlucky_record = $unlucky_record->fresh();
        $this->assertEquals($unlucky_record->account->name, 'ThisIsAPrefix' . $before_changes->name);
        $this->assertEquals($unlucky_record->organisation_type_id, $new_organisation_type->id);
        $this->assertEquals($unlucky_record->property_manager_id, $new_property_manager->id);
        $this->assertEquals($unlucky_record->client_account_status_id, $new_client_account_status->id);
        $this->assertEquals($unlucky_record->name, 'ThisIsAPrefix' . $before_changes->name);
        $this->assertEquals($unlucky_record->yardi_client_ref, 'pfx' . $before_changes->yardi_client_ref);
        $this->assertEquals($unlucky_record->yardi_alt_ref, 'pfx' . $before_changes->yardi_alt_ref);

        $freshAddrFields = $unlucky_record->address->toAddrArray();
        foreach (array_keys($freshAddrFields, 'addr_') as $key) {
            $this->assertEquals($freshAddrFields[$key], $new_address[$key]);
        }
    }

    public function testDoesClientAccountsControllerValidateProperly()
    {
        $tooLongRef = 'ThisReferenceMightBeABitTooLongForTheValidationSoItShouldFail123123123123123123123123123';
        // Empty name will fail
        $this->apiAs($this->dev_user, 'POST', '/api/client/client-accounts/', [
            'account_id'               => -1,
            'organisation_type_id'     => -1,
            'address_id'               => -1,
            'property_manager_id'      => -1,
            'client_account_status_id' => -1,
            'name'                     => '',
            'yardi_client_ref'         => '',
            'yardi_alt_ref'            => $tooLongRef
        ], [])->assertJsonValidationErrors([
            'organisation_type_id',
            'property_manager_id',
            'client_account_status_id',
            'name',
            'yardi_client_ref',
            'yardi_alt_ref'
        ]);
    }

    public function testCanUsersDeleteClientAccounts()
    {
        $unlucky_record = (factory(ClientAccount::class, 1)->create())->first();
        $this->assertInstanceOf(ClientAccount::class, $unlucky_record);
        $this->apiAs(
            $this->dev_user,
            'DELETE',
            '/api/client/client-accounts/' . $unlucky_record->id,
            [],
            []
        )->assertSuccessful();
        $this->assertNull(ClientAccount::find($unlucky_record->id));
    }

    public function testCanUsersPaginateClientAccountsDataTable()
    {
        // Try with two values to avoid the .00001% chance the size of the full table is the attempted page size
        $result = $this->apiAs($this->dev_user, 'GET', '/api/client/client-accounts/data-table', [
            'limit' => 2,
        ], []);
        $result->assertSuccessful();
        $this->assertSame(2, count($result->json()['rows']));

        $result = $this->apiAs($this->dev_user, 'GET', '/api/client/client-accounts/data-table', [
            'limit' => 3,
        ], []);
        $result->assertSuccessful();
        $this->assertSame(3, count($result->json()['rows']));
    }

    public function testCanUsersSortClientAccountsDataTable()
    {
        // Try with two values to avoid the .00001% chance the size of the full table is the attempted page size
        $result = $this->apiAs($this->dev_user, 'GET', '/api/client/client-accounts/data-table', [
            'sort_column' => 'id',
            'sort_order'  => 'desc',
            'limit'       => 1
        ], []);
        $result->assertSuccessful();
        $this->assertSame(
            ClientAccount::orderBy(ClientAccount::getTableName() . '.id', 'desc')->first()->id,
            $result->json()['rows'][0]['id']
        );

        $result = $this->apiAs($this->dev_user, 'GET', '/api/client/client-accounts/data-table', [
            'sort_column' => 'id',
            'sort_order'  => 'asc',
            'limit'       => 1
        ], []);
        $result->assertSuccessful();
        $this->assertSame(
            ClientAccount::orderBy(ClientAccount::getTableName() . '.id', 'asc')->first()->id,
            $result->json()['rows'][0]['id']
        );
    }

    public function testCanUsersFilterClientAccountsDataTable()
    {
        $filter_clientAccountStatus = ClientAccountStatus::randomRow();
        $filter_property_manager    = PropertyManager::randomRow();
        $filter_organisation_type   = OrganisationType::randomRow();

        // Check them all, why not?
        $total_rows = ClientAccount::count();

        $result = $this->apiAs($this->dev_user, 'GET', '/api/client/client-accounts/data-table', [
            'limit'               => $total_rows,
            'property_manager_id' => $filter_property_manager->id,
        ], []);
        $result->assertSuccessful();
        foreach ($result->json()['rows'] as $item) {
            $this->assertSame($filter_property_manager->id, $item['property_manager_id']);
        }

        $result = $this->apiAs($this->dev_user, 'GET', '/api/client/client-accounts/data-table', [
            'limit'                    => $total_rows,
            'client_account_status_id' => $filter_clientAccountStatus->id,
        ], []);
        $result->assertSuccessful();
        foreach ($result->json()['rows'] as $item) {
            $this->assertSame($filter_clientAccountStatus->id, $item['client_account_status_id']);
        }

        $result = $this->apiAs($this->dev_user, 'GET', '/api/client/client-accounts/data-table', [
            'limit'                => $total_rows,
            'organisation_type_id' => $filter_organisation_type->id,
        ], []);
        $result->assertSuccessful();
        foreach ($result->json()['rows'] as $item) {
            $this->assertSame($filter_organisation_type->id, $item['organisation_type_id']);
        }

        $result = $this->apiAs($this->dev_user, 'GET', '/api/client/client-accounts/data-table', [
            'client_account_name_partial'         => 'a',
        ], []);
        $result->assertSuccessful();
        foreach ($result->json()['rows'] as $item) {
            $this->assertContains('a', strtolower($item['name']));
        }
        $result = $this->apiAs($this->dev_user, 'GET', '/api/client/client-accounts/data-table', [
            'client_account_name_partial'         => 'b',
        ], []);
        $result->assertSuccessful();
        foreach ($result->json()['rows'] as $item) {
            $this->assertContains('b', strtolower($item['name']));
        }
        $result = $this->apiAs($this->dev_user, 'GET', '/api/client/client-accounts/data-table', [
            'client_account_name_partial'         => 'c',
        ], []);
        $result->assertSuccessful();
        foreach ($result->json()['rows'] as $item) {
            $this->assertContains('c', strtolower($item['name']));
        }
    }

    public function testCanUsersReadClientAccountsdataTable()
    {
        $result = $this->apiAs($this->dev_user, 'GET', '/api/client/client-accounts/data-table', [
            'limit' => 1,
        ], []);
        $result->assertSuccessful();

        $result->assertJsonStructure([
            'rows' => [
                '*' => [
                    'name',
                    'yardi_client_ref',
                    'yardi_alt_ref',
                    'property_manager'      => [
                        'user' => [
                            'role'
                        ]
                    ],
                    'client_account_status' => [
                        'id', 'name'
                    ],
                    'organisation_type'     => [
                        'id', 'name'
                    ]
                ]
            ],
            'row_count'
        ]);
    }
}
