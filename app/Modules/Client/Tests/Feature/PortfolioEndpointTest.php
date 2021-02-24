<?php
namespace App\Modules\Client\Tests;

use App\Modules\Client\Models\ClientAccount;
use App\Modules\Client\Models\Portfolio;
use App\Modules\Common\Classes\EndpointTest;
use App\Modules\Common\Traits\TestsCardSummaryData;

class PortfolioEndpointTest extends EndpointTest
{
    use TestsCardSummaryData;

    public function testCanUsersIndexPortfolios()
    {
        $this->assertGetCountForAllUserTypes('/api/client/portfolios/', Portfolio::query());
    }

    public function testCanUsersReadPortfolios()
    {
        $existing_id = Portfolio::first()->id;
        $this->assertGetForAllUserTypes('/api/client/portfolios/' . $existing_id);
        $this->apiAs($this->dev_user, 'GET', '/api/client/portfolios/-1', [], [])->assertStatus(404);
    }

    public function testCanUsersCreatePortfolios()
    {
        $this->apiAs($this->dev_user, 'POST', '/api/client/portfolios/', [
            'client_account_id'   => ClientAccount::randomRow()->id,
            'name'                => 'GottaMakeItImpossibleToCollideAmirite',
            'yardi_portfolio_ref' => 'uzumymw'
        ], [])->assertSuccessful();

        $this->assertInstanceOf(
            Portfolio::class,
            Portfolio::where('name', 'GottaMakeItImpossibleToCollideAmirite')->first()
        );
    }

    public function testCanUsersUpdatePortfolios()
    {
        // Select a random record
        $unlucky_record       = Portfolio::randomRow();
        $before_changes       = clone $unlucky_record;
        $rando_client_account = ClientAccount::randomRow();

        // Make a patch request
        $this->apiAs($this->authoriser_user, 'PATCH', '/api/client/portfolios/' . $unlucky_record->id, [
            'client_account_id'   => $rando_client_account->id,
            'name'                => 'ThisIsAPrefix' . $unlucky_record->name,
            'yardi_portfolio_ref' => substr($unlucky_record->yardi_portfolio_ref, 0, -1),
            'edit'                => false
        ], [])->assertSuccessful();

        $this->assertUpdate($unlucky_record, $rando_client_account, $before_changes);

        $this->apiAs($this->authoriser_user, 'PATCH', '/api/client/portfolios/' . $unlucky_record->id, [
            'client_account_id'   => $rando_client_account->id,
            'name'                => 'ThisIsAPrefix' . $unlucky_record->name,
            'yardi_portfolio_ref' => substr($unlucky_record->yardi_portfolio_ref, 0, -1),
            'edit'                => true
        ], [])->assertSuccessful();

        $this->assertUpdate($unlucky_record, $rando_client_account, $before_changes);
    }

    private function assertUpdate($unlucky_record, $rando_client_account, $before_changes)
    {
        // Check if changes happened
        $unlucky_record = $unlucky_record->fresh();
        $this->assertEquals($unlucky_record->client_account_id, $rando_client_account->id);
        $this->assertEquals($unlucky_record->name, 'ThisIsAPrefix' . $before_changes->name);
        $this->assertEquals($unlucky_record->yardi_portfolio_ref, substr($before_changes->yardi_portfolio_ref, 0, -1));
    }

    public function testDoesPortfoliosControllerValidateProperly()
    {
        $submitted_data = [
            'client_account_id'   => -1,
            'name'                => '',
            'yardi_portfolio_ref' => ''
        ];

        // Check if all errors are present
        $this->apiAs(
            $this->dev_user,
            'POST',
            '/api/client/portfolios/',
            $submitted_data,
            []
        )->assertJsonValidationErrors(array_keys($submitted_data));
    }

    public function testCanUsersDeletePortfolios()
    {
        $unlucky_record = Portfolio::randomRow();
        $this->assertInstanceOf(Portfolio::class, $unlucky_record);
        $this->apiAs(
            $this->dev_user,
            'DELETE',
            '/api/client/portfolios/' . $unlucky_record->id,
            [],
            []
        )->assertSuccessful();
        $this->assertNull(Portfolio::find($unlucky_record->id));
    }

    public function testCanUsersPaginatePortfoliosDataTable()
    {
        // Try with two values to avoid the .00001% chance the size of the full table is the attempted page size
        $result = $this->apiAs($this->dev_user, 'GET', '/api/client/portfolios/data-table', [
            'limit' => 2,
        ], []);
        $result->assertSuccessful();
        $this->assertSame(2, count($result->json()['rows']));

        $result = $this->apiAs($this->dev_user, 'GET', '/api/client/portfolios/data-table', [
            'limit' => 3,
        ], []);
        $result->assertSuccessful();
        $this->assertSame(3, count($result->json()['rows']));
    }

    public function testCanUsersSortPortfoliosDataTable()
    {
        // Try with two values to avoid the .00001% chance the size of the full table is the attempted page size
        $result = $this->apiAs($this->dev_user, 'GET', '/api/client/portfolios/data-table', [
            'sort_column' => 'id',
            'sort_order'  => 'desc',
            'limit'       => 1
        ], []);
        $result->assertSuccessful();
        $this->assertSame(
            Portfolio::orderBy(Portfolio::getTableName() . '.id', 'desc')->first()->id,
            $result->json()['rows'][0]['id']
        );

        $result = $this->apiAs($this->dev_user, 'GET', '/api/client/portfolios/data-table', [
            'sort_column' => 'id',
            'sort_order'  => 'asc',
            'limit'       => 1
        ], []);
        $result->assertSuccessful();
        $this->assertSame(
            Portfolio::orderBy(Portfolio::getTableName() . '.id', 'asc')->first()->id,
            $result->json()['rows'][0]['id']
        );
    }

    public function testCanUsersFilterPortfoliosDataTable()
    {
        $filter_clientAccount = ClientAccount::randomRow();

        // Check client_account_id filter
        $result = $this->apiAs($this->dev_user, 'GET', '/api/client/portfolios/data-table', [
            'limit'             => 10,
            'client_account_id' => $filter_clientAccount->id,
        ], []);
        $result->assertSuccessful();
        foreach ($result->json()['rows'] as $item) {
            $this->assertSame($filter_clientAccount->id, $item['client_account_id']);
        }

        $result = $this->apiAs($this->dev_user, 'GET', '/api/client/portfolios/data-table', [
            'portfolio_name_partial'         => 'a',
        ], []);
        $result->assertSuccessful();
        foreach ($result->json()['rows'] as $item) {
            $this->assertContains('a', strtolower($item['name']));
        }
        $result = $this->apiAs($this->dev_user, 'GET', '/api/client/portfolios/data-table', [
            'portfolio_name_partial'         => 'b',
        ], []);
        $result->assertSuccessful();
        foreach ($result->json()['rows'] as $item) {
            $this->assertContains('b', strtolower($item['name']));
        }
        $result = $this->apiAs($this->dev_user, 'GET', '/api/client/portfolios/data-table', [
            'portfolio_name_partial'         => 'c',
        ], []);
        $result->assertSuccessful();
        foreach ($result->json()['rows'] as $item) {
            $this->assertContains('c', strtolower($item['name']));
        }
    }

    public function testCanUsersReadPortfoliosDataTable()
    {
        $result = $this->apiAs($this->dev_user, 'GET', '/api/client/portfolios/data-table', [
            'limit' => Portfolio::count(),
        ], []);
        $result->assertSuccessful();

        $result->assertJsonStructure([
            'rows' => [
                '*' => [
                    'id',
                    'name',
                    'yardi_portfolio_ref',
                    'client_account' => [
                        'id', 'name', 'yardi_client_ref', 'yardi_alt_ref'
                    ]
                ]
            ],
            'row_count'

        ]);
    }
}
