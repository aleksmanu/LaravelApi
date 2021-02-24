<?php
namespace App\Modules\Property\Tests\Feature;

use App\Modules\Common\Classes\EndpointTest;
use App\Modules\Property\Models\PropertyManager;

class PropertyManagerEndpointTest extends EndpointTest
{
    public function testCanUsersIndexPropertyManagers()
    {
        $this->assertGetCountForAllUserTypes('/api/property/property-managers/', PropertyManager::query());
    }

    public function testCanUsersReadPropertyManagers()
    {
        $existing_id = PropertyManager::first()->id;
        $this->assertGetForAllUserTypes('/api/property/property-managers/' . $existing_id);
        $this->apiAs($this->dev_user, 'GET', '/api/property/property-managers/-1', [], [])->assertStatus(404);
    }

    public function testCanUsersCreatePropertyManagers()
    {
        $this->apiAs($this->dev_user, 'POST', '/api/property/property-managers/', [
            'user_id' => $this->dev_user->id,
            'code' => 'abc123#'
        ], [])->assertSuccessful();

        $this->assertInstanceOf(
            PropertyManager::class,
            PropertyManager::where('user_id', $this->dev_user->id)
                ->where('code', 'abc123#')
                ->first()
        );
    }

    public function testCanUsersUpdatePropertyManagers()
    {
        // Select a random record
        $unlucky_record = PropertyManager::randomRow();
        $before_changes = clone $unlucky_record;

        // Make a patch request
        $this->apiAs($this->dev_user, 'PATCH', '/api/property/property-managers/' . $unlucky_record->id, [
            'user_id' => $this->slave_user->id,
            // max:10 here, watch out
            'code' => '$^' . $unlucky_record->code,
        ], [])->assertSuccessful();

        // Check if changes happened
        $unlucky_record = $unlucky_record->fresh();
        $this->assertEquals($unlucky_record->user_id, $this->slave_user->id);
        $this->assertEquals($unlucky_record->code, '$^' . $before_changes->code);
    }

    public function testDoesPropertyManagerControllerValidateProperly()
    {
        // Empty name, non-existent user will fail
        $this->apiAs($this->dev_user, 'POST', '/api/property/property-managers/', [
            'user_id' => 0,
            'code' => ''
        ], [])->assertJsonValidationErrors([
            'user_id', 'code'
        ]);

        // Proper name & user_id will succeed
        $this->apiAs($this->dev_user, 'POST', '/api/property/property-managers/', [
            'user_id' => $this->dev_user->id,
            'code' => 'Un1qu3'
        ], [])->assertSuccessful();

        // Same name as before will fail as a duplicate
        $this->apiAs($this->dev_user, 'POST', '/api/property/property-managers/', [
            'user_id' => $this->dev_user->id,
            'code' => 'Un1qu3'
        ], [])->assertJsonValidationErrors([
            'user_id'
        ]);
    }

    public function testCanUsersDeletePropertyManagers()
    {
        $unlucky_record = (factory(PropertyManager::class, 1)->create())->first();
        $this->assertInstanceOf(PropertyManager::class, $unlucky_record);
        $this->apiAs(
            $this->dev_user,
            'DELETE',
            '/api/property/property-managers/' . $unlucky_record->id,
            [],
            []
        )->assertSuccessful();
        $this->assertNull(PropertyManager::find($unlucky_record->id));
    }

    public function testCanUsersPaginatePropertyManagersDataTable()
    {
        // Try with two values to avoid the .00001% chance the size of the full table is the attempted page size
        $result = $this->apiAs($this->dev_user, 'GET', '/api/property/property-managers/data-table', [
            'limit'           => 2,
        ], []);
        $result->assertSuccessful();
        $this->assertSame(2, count($result->json()));

        $result = $this->apiAs($this->dev_user, 'GET', '/api/property/property-managers/data-table', [
            'limit'           => 3,
        ], []);
        $result->assertSuccessful();
        $this->assertSame(3, count($result->json()));
    }

    public function testCanUsersSortPropertyManagersDataTable()
    {
        // Try with two values to avoid the .00001% chance the size of the full table is the attempted page size
        $result = $this->apiAs($this->dev_user, 'GET', '/api/property/property-managers/data-table', [
            'sort_column' => 'id',
            'sort_order'  => 'desc',
            'limit'       => 1
        ], []);
        $result->assertSuccessful();
        $this->assertSame(
            PropertyManager::orderBy(PropertyManager::getTableName().'.id', 'desc')->first()->id,
            $result->json()[0]['id']
        );

        $result = $this->apiAs($this->dev_user, 'GET', '/api/property/property-managers/data-table', [
            'sort_column' => 'id',
            'sort_order'  => 'asc',
            'limit'       => 1
        ], []);
        $result->assertSuccessful();
        $this->assertSame(
            PropertyManager::orderBy(PropertyManager::getTableName().'.id', 'asc')->first()->id,
            $result->json()[0]['id']
        );
    }

//    public function testCanUsers_filter_propertyManagersDataTable() {
//        $filter_clientAccount = ClientAccount::randomRow();
//
//        // Check client_account_id filter
//        $result = $this->apiAs($this->dev_user, 'GET', '/api/property/property-managers/data-table', [
//            'limit' => 10,
//            'client_id'           => $filter_clientAccount->id,
//        ], []);
//        $result->assertSuccessful();
//        foreach($result->json() as $item) {
//            $this->assertSame($filter_clientAccount->id, $item['portfolio']['client_account_id']);
//        }
//    }

    public function testCanUsersReadPropertyManagersDataTable()
    {
        $result = $this->apiAs($this->dev_user, 'GET', '/api/property/property-managers/data-table', [
            'limit' => PropertyManager::count(),
        ], []);
        $result->assertSuccessful();

        $result->assertJsonStructure([
            '*' => [
                'id', 'first_name', 'last_name', 'email', 'client_accounts_count', 'properties_count', 'units_count'
            ]
        ]);
    }
}
