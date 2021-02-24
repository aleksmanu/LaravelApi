<?php
namespace App\Modules\Property\Tests\Feature;

use App\Modules\Auth\Models\User;
use App\Modules\Client\Models\ClientAccount;
use App\Modules\Client\Models\Portfolio;
use App\Modules\Common\Classes\EndpointTest;
use App\Modules\Common\Traits\TestsCardSummaryData;
use App\Modules\Common\Traits\TestsEndpoints;
use App\Modules\Property\Models\MeasurementUnit;
use App\Modules\Property\Models\Property;
use App\Modules\Property\Models\PropertyManager;
use App\Modules\Property\Models\Unit;
use Tests\TestCase;

class UnitEndpointTest extends EndpointTest
{
    use TestsCardSummaryData;

    public function testCanUsersIndexUnits()
    {
        $this->assertGetCountForAllUserTypes('/api/property/units/', Unit::query()->where('is_virtual', 0));
    }

    public function testCanUsersReadUnits()
    {
        $existing_id = Unit::first()->id;
        $this->assertGetForAllUserTypes('/api/property/units/' . $existing_id);
        $this->apiAs($this->dev_user, 'GET', '/api/property/units/-1', [], [])->assertStatus(404);
    }

    public function testCanUsersCreateUnits()
    {
        $this->apiAs($this->dev_user, 'POST', '/api/property/units/', [
            'property_id'             => Property::randomRow()->id,
            'property_manager_id'     => PropertyManager::randomRow()->id,
            'measurement_unit_id'     => MeasurementUnit::randomRow()->id,
            'demise'                  => 'whatever',
            'unit'                    => 'asdf',
            'name'                    => 'GottaMakeItImpossibleToCollideAmirite',
            'yardi_unit_ref'          => 'SomeRandomString',
            'yardi_import_ref'        => 'SomeRandomString',
            'yardi_property_unit_ref' => 'SomeRandomString',
            'measurement_value'       => 1337,
            'approved_at'             => '2000-01-01',
            'approved'                => true,
            'approved_initials'       => $this->dev_user->calculateInitials(),
            'held_at'                 => '2000-01-01',
            'held_initials'           => $this->dev_user->calculateInitials()
        ], [])->assertSuccessful();

        $this->assertInstanceOf(Unit::class, Unit::where('name', 'GottaMakeItImpossibleToCollideAmirite')->first());
    }

    public function testCanUsersUpdateUnits()
    {
        // Select a random record
        $unlucky_record = Unit::randomRow();
        $before_changes = clone $unlucky_record;

        $rando_property         = Property::randomRow();
        $rando_property_manager = PropertyManager::randomRow();
        $rando_measurement_unit = MeasurementUnit::randomRow();
        $rando_user             = User::randomRow();

        $data = [
            'property_id'             => $rando_property->id,
            'property_manager_id'     => $rando_property_manager->id,
            'measurement_unit_id'     => $rando_measurement_unit->id,
            'demise'                  => '$' . $unlucky_record->demise,
            'unit'                    => '$' . $unlucky_record->unit,
            'name'                    => 'ThisIsAPrefix' . $unlucky_record->name,
            'yardi_unit_ref'          => 'SomeRandomString2',
            'yardi_import_ref'        => 'SomeRandomString2',
            'yardi_property_unit_ref' => 'SomeRandomString2',
            'measurement_value'       => $unlucky_record->measurement_value + 999,
            'approved_at'             => '2000-01-01',
            'approved'                => true,
            'approved_initials'       => $this->dev_user->calculateInitials(),
            'held_at'                 => '2000-01-01',
            'held_initials'           => $this->dev_user->calculateInitials(),
            'edit'                    => false
        ];
        // Make a patch request
        $this->apiAs(
            $this->authoriser_user,
            'PATCH',
            '/api/property/units/' . $unlucky_record->id,
            $data,
            []
        )->assertSuccessful();
        $this->assertUpdate(
            $unlucky_record,
            $rando_property,
            $rando_property_manager,
            $rando_measurement_unit,
            $before_changes
        );

        $data['edit'] = true;

        $this->apiAs(
            $this->authoriser_user,
            'PATCH',
            '/api/property/units/' . $unlucky_record->id,
            $data,
            []
        )->assertSuccessful();
        $this->assertUpdate(
            $unlucky_record,
            $rando_property,
            $rando_property_manager,
            $rando_measurement_unit,
            $before_changes
        );
    }

    /**
     * Check if expected record update changes happened
     *
     * @param Unit $unlucky_record
     * @param Property $rando_property
     * @param PropertyManager $rando_property_manager
     * @param MeasurementUnit $rando_measurement_unit
     * @param Unit $before_changes
     * @return void
     */
    private function assertUpdate(
        $unlucky_record,
        $rando_property,
        $rando_property_manager,
        $rando_measurement_unit,
        $before_changes
    ) {
        $unlucky_record = $unlucky_record->fresh();
        $this->assertEquals($unlucky_record->property_id, $rando_property->id);
        $this->assertEquals($unlucky_record->property_manager_id, $rando_property_manager->id);
        $this->assertEquals($unlucky_record->measurement_unit_id, $rando_measurement_unit->id);
        $this->assertEquals($unlucky_record->demise, '$' . $before_changes->demise);
        $this->assertEquals($unlucky_record->unit, '$' . $before_changes->unit);
        $this->assertEquals($unlucky_record->name, 'ThisIsAPrefix' . $before_changes->name);
        $this->assertEquals($unlucky_record->measurement_value, $before_changes->measurement_value + 999);
    }

    public function testDoesUnitControllerValidateProperly()
    {
        $submitted_data = [
            'property_id'             => -1,
            'property_manager_id'     => -1,
            'measurement_unit_id'     => -1,
            'demise'                  => '',
            'yardi_unit_ref'          => true,
            'yardi_import_ref'        => true,
            'yardi_property_unit_ref' => true,
            'measurement_value'       => 'notANumeric',
            'approved_at'             => 'notADate',
            'approved'                => 'NotABoolean',
            'approved_initials'       => 9191919,
            'held_at'                 => 'notADate',
            'held_initials'           => 9191919
        ];

        // Check if all errors are present
        $this->apiAs(
            $this->dev_user,
            'POST',
            '/api/property/units/',
            $submitted_data,
            []
        )->assertJsonValidationErrors(array_keys($submitted_data));
    }

    public function testCanUsersDeleteUnits()
    {
        $unlucky_record = (factory(Unit::class, 1)->create([
            'property_id'         => Property::randomRow()->id,
            'property_manager_id' => PropertyManager::randomRow()->id
        ]))->first();
        $this->assertInstanceOf(Unit::class, $unlucky_record);
        $this->apiAs(
            $this->dev_user,
            'DELETE',
            '/api/property/units/' . $unlucky_record->id,
            [],
            []
        )->assertSuccessful();
        $this->assertNull(Unit::find($unlucky_record->id));
    }

    public function testCanUsersPaginateUnitsDataTable()
    {
        // Try with two values to avoid the .00001% chance the size of the full table is the attempted page size
        $result = $this->apiAs($this->dev_user, 'GET', '/api/property/units/data-table', [
            'limit' => 2,
        ], []);
        $result->assertSuccessful();
        $this->assertSame(2, count($result->json()['rows']));

        $result = $this->apiAs($this->dev_user, 'GET', '/api/property/units/data-table', [
            'limit' => 3,
        ], []);
        $result->assertSuccessful();
        $this->assertSame(3, count($result->json()['rows']));
    }

    public function testCanUsersSortUnitsDataTable()
    {
        // Try with two values to avoid the .00001% chance the size of the full table is the attempted page size
        $result = $this->apiAs($this->dev_user, 'GET', '/api/property/units/data-table', [
            'sort_column' => 'id',
            'sort_order'  => 'desc',
            'limit'       => 1
        ], []);
        $result->assertSuccessful();
        $this->assertSame(
            Unit::orderBy(Unit::getTableName().'.id', 'desc')->where('is_virtual', 0)->first()->id,
            $result->json()['rows'][0]['id']
        );

        $result = $this->apiAs($this->dev_user, 'GET', '/api/property/units/data-table', [
            'sort_column' => 'id',
            'sort_order'  => 'asc',
            'limit'       => 1
        ], []);
        $result->assertSuccessful();
        $this->assertSame(
            Unit::orderBy(Unit::getTableName().'.id', 'asc')->where('is_virtual', 0)->first()->id,
            $result->json()['rows'][0]['id']
        );
    }

    public function testCanUsersFilterUnitsDataTable()
    {
        $filter_clientAccount    = ClientAccount::randomRow();
        $filter_portfolio        = Portfolio::randomRow();
        $filter_property_manager = PropertyManager::randomRow();

        // Check client_account_id filter
        $result = $this->apiAs($this->dev_user, 'GET', '/api/property/units/data-table', [
            'limit'             => 10,
            'client_account_id' => $filter_clientAccount->id,
        ], []);
        $result->assertSuccessful();
        foreach ($result->json()['rows'] as $item) {
            $this->assertSame($filter_clientAccount->id, $item['property']['portfolio']['client_account_id']);
        }

        // Check property_manager_id filter
        $result = $this->apiAs($this->dev_user, 'GET', '/api/property/units/data-table', [
            'limit'               => 10,
            'property_manager_id' => $filter_property_manager->id,
        ], []);
        $result->assertSuccessful();
        foreach ($result->json()['rows'] as $item) {
            $this->assertSame($filter_property_manager->id, $item['property_manager_id']);
        }

        // Check portfolio_id filter
        $result = $this->apiAs($this->dev_user, 'GET', '/api/property/units/data-table', [
            'limit'        => 10,
            'portfolio_id' => $filter_portfolio->id,
        ], []);
        $result->assertSuccessful();
        foreach ($result->json()['rows'] as $item) {
            $this->assertSame($filter_portfolio->id, $item['property']['portfolio_id']);
        }

        $result = $this->apiAs($this->dev_user, 'GET', '/api/property/units/data-table', [
            'property_name_partial'         => 'a',
        ], []);
        $result->assertSuccessful();
        foreach ($result->json()['rows'] as $item) {
            $this->assertContains('a', strtolower($item['property']['name'] . $item['name'] . $item['demise']));
        }
        $result = $this->apiAs($this->dev_user, 'GET', '/api/property/units/data-table', [
            'property_name_partial'         => 'b',
        ], []);
        $result->assertSuccessful();
        foreach ($result->json()['rows'] as $item) {
            $this->assertContains('b', strtolower($item['property']['name'] . $item['name'] . $item['demise']));
        }
        $result = $this->apiAs($this->dev_user, 'GET', '/api/property/units/data-table', [
            'property_name_partial'         => 'c',
        ], []);
        $result->assertSuccessful();
        foreach ($result->json()['rows'] as $item) {
            $this->assertContains('c', strtolower($item['property']['name'] . $item['name'] . $item['demise']));
        }
    }

    public function testCanUsersReadUnitsDataTable()
    {
        $result = $this->apiAs($this->dev_user, 'GET', '/api/property/units/data-table', [
            'limit' => Unit::count() % 100,//Let's keep these reasonable
        ], []);
        $result->assertSuccessful();

        $result->assertJsonStructure([
            'rows' => [
                '*' => [
                    'id',
                    'property'         => [
                        'property_status_id',
                        'portfolio' => [
                            'client_account'
                        ],
                        'address'   => [
                            'county',
                            'country',
                        ],
                    ],
                    'property_manager' => [
                        'user' => [
                            'role'
                        ]
                    ],
                ]
            ], 'row_count'

        ]);
    }
}
