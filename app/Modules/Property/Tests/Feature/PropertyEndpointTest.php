<?php
namespace App\Modules\Property\Tests\Feature;

use App\Modules\Auth\Models\User;
use App\Modules\Client\Models\ClientAccount;
use App\Modules\Client\Models\Portfolio;
use App\Modules\Common\Classes\EndpointTest;
use App\Modules\Common\Models\Address;
use App\Modules\Common\Traits\TestsCardSummaryData;
use App\Modules\Property\Models\LocationType;
use App\Modules\Property\Models\Property;
use App\Modules\Property\Models\PropertyCategory;
use App\Modules\Property\Models\PropertyManager;
use App\Modules\Property\Models\PropertyStatus;
use App\Modules\Property\Models\PropertyUse;
use App\Modules\Property\Models\PropertyTenure;
use App\Modules\Property\Models\StopPosting;

class PropertyEndpointTest extends EndpointTest
{
    use TestsCardSummaryData;

    public function testCanUsersIndexProperties()
    {
        $this->assertGetCountForAllUserTypes('/api/property/properties/', Property::query());
    }

    public function testCanUsersReadProperties()
    {
        $existing_id = Property::randomRow()->id;
        $this->assertGetForAllUserTypes('/api/property/properties/' . $existing_id);
        $this->apiAs($this->dev_user, 'GET', '/api/property/properties/-1', [], [])->assertStatus(404);
    }

    public function testCanUsersCreateProperties()
    {
        $this->apiAs(
            $this->dev_user,
            'POST',
            '/api/property/properties/',
            [
                'portfolio_id'              => Portfolio::first()->id,
                'property_manager_id'       => PropertyManager::first()->id,
                'address_id'                => Address::first()->id,
                'property_status_id'        => PropertyStatus::first()->id,
                'property_use_id'           => PropertyUse::first()->id,
                'property_tenure_id'        => PropertyTenure::first()->id,
                'location_type_id'          => LocationType::first()->id,
                'property_category_id'      => PropertyCategory::first()->id,
                'stop_posting_id'           => StopPosting::first()->id,
                'name'                      => 'GottaMakeItImpossibleToCollideAmirite',
                'yardi_property_ref'        => 'leavemealone',
                'yardi_alt_ref'             => 'panzer',
                'total_lettable_area'       => 100,
                'void_total_lettable_area'  => 100,
                'total_site_area'           => 100,
                'total_gross_internal_area' => 100,
                'total_rateable_value'      => 100,
                'void_total_rateable_value' => 100,
                'listed_building'           => true,
                'stop_posting'              => true,
                'live'                      => true,
                'conservation_area'         => true,
                'air_conditioned'           => true,
                'vat_registered'            => true,
                'approved'                  => true,
                'approved_at'               => '2015-04-20',
                'approved_initials'         => $this->dev_user->calculateInitials(),
                'held_initials'             => $this->dev_user->calculateInitials(),
                'held_at'                   => '2015-04-20',
            ] + Address::randomRow()->toAddrArray(),
            []
        )->assertSuccessful();

        $this->assertInstanceOf(
            Property::class,
            Property::where('name', 'GottaMakeItImpossibleToCollideAmirite')->first()
        );
    }

    public function testCanUsersUpdateProperties()
    {
        // Select a random record
        $unlucky_record       = Property::randomRow();
        $before_changes       = clone $unlucky_record;
        $new_property_manager = factory(PropertyManager::class, 1)->create()->first();
        $new_user             = factory(User::class, 1)->create()->first();
        $new_property_status  = factory(PropertyStatus::class, 1)->create()->first();
        $new_property_use     = factory(PropertyUse::class, 1)->create()->first();
        $new_tenure           = factory(PropertyTenure::class, 1)->create()->first();
        $new_location_type    = factory(LocationType::class, 1)->create()->first();
        $stop_posting         = StopPosting::first();
        $property_category    = PropertyCategory::first();

        $new_portfolio = factory(Portfolio::class, 1)->create([
            'client_account_id' => ClientAccount::first()->id,
        ])->first();

        $data = [
            'portfolio_id'              => $new_portfolio->id,
            'property_manager_id'       => $new_property_manager->id,
            'property_status_id'        => $new_property_status->id,
            'property_use_id'           => $new_property_use->id,
            'property_tenure_id'        => $new_tenure->id,
            'location_type_id'          => $new_location_type->id,
            'property_category_id'      => $property_category->id,
            'stop_posting_id'           => $stop_posting->id,
            'name'                      => $unlucky_record->name . 'suf',
            'yardi_property_ref'        => $unlucky_record->yardi_property_ref . 'suf',
            'yardi_alt_ref'             => $unlucky_record->yardi_alt_ref . 'suf',
            'total_lettable_area'       => $unlucky_record->total_lettable_area + 1337,
            'void_total_lettable_area'  => $unlucky_record->void_total_lettable_area + 1337,
            'total_site_area'           => $unlucky_record->total_site_area + 1337,
            'total_gross_internal_area' => $unlucky_record->total_gross_internal_area + 1337,
            'total_rateable_value'      => $unlucky_record->total_rateable_value + 1337,
            'void_total_rateable_value' => $unlucky_record->void_total_rateable_value + 1337,
            'listed_building'           => !$unlucky_record->listed_building,
            'stop_posting'              => !$unlucky_record->stop_posting,
            'live'                      => !$unlucky_record->live,
            'conservation_area'         => !$unlucky_record->conservation_area,
            'air_conditioned'           => !$unlucky_record->air_conditioned,
            'vat_registered'            => !$unlucky_record->vat_registered,
            'approved'                  => !$unlucky_record->approved,
            'approved_at'               => '2015-04-20',
            'approved_initials'         => $new_user->calculateInitials(),
            'held_initials'             => $new_user->calculateInitials(),
            'held_at'                   => '2015-04-20',
            'edit'                      => false
        ] + Address::randomRow()->toAddrArray();

        // Make a patch request
        $this->apiAs(
            $this->authoriser_user,
            'PATCH',
            '/api/property/properties/' . $unlucky_record->id,
            $data,
            []
        )->assertSuccessful();
        $this->assertUpdate(
            $unlucky_record,
            $new_portfolio,
            $stop_posting,
            $new_property_manager,
            $new_property_status,
            $new_property_use,
            $new_tenure,
            $new_location_type,
            $property_category,
            $before_changes,
            $new_user
        );

        $data['edit'] = true;
        $this->apiAs(
            $this->dev_user,
            'PATCH',
            '/api/property/properties/' . $unlucky_record->id,
            $data,
            []
        )->assertSuccessful();
        $this->assertUpdate(
            $unlucky_record,
            $new_portfolio,
            $stop_posting,
            $new_property_manager,
            $new_property_status,
            $new_property_use,
            $new_tenure,
            $new_location_type,
            $property_category,
            $before_changes,
            $new_user
        );
    }

    private function assertUpdate(
        $unlucky_record,
        $new_portfolio,
        $stop_posting,
        $new_property_manager,
        $new_property_status,
        $new_property_use,
        $new_tenure,
        $new_location_type,
        $property_category,
        $before_changes,
        $new_user
    ) {
        // Check if changes happened
        $unlucky_record = $unlucky_record->fresh();
        $this->assertEquals($unlucky_record->portfolio_id, $new_portfolio->id);
        $this->assertEquals($unlucky_record->stop_posting_id, $stop_posting->id);
        $this->assertEquals($unlucky_record->property_manager_id, $new_property_manager->id);
        $this->assertEquals($unlucky_record->property_status_id, $new_property_status->id);
        $this->assertEquals($unlucky_record->property_use_id, $new_property_use->id);
        $this->assertEquals($unlucky_record->property_tenure_id, $new_tenure->id);
        $this->assertEquals($unlucky_record->location_type_id, $new_location_type->id);
        $this->assertEquals($unlucky_record->property_category_id, $property_category->id);
        $this->assertEquals($unlucky_record->name, $before_changes->name . 'suf');
        $this->assertEquals($unlucky_record->yardi_property_ref, $before_changes->yardi_property_ref . 'suf');
        $this->assertEquals($unlucky_record->yardi_alt_ref, $before_changes->yardi_alt_ref . 'suf');
        $this->assertEquals($unlucky_record->total_lettable_area, $before_changes->total_lettable_area + 1337);
        $this->assertEquals($unlucky_record->total_site_area, $before_changes->total_site_area + 1337);
        $this->assertEquals($unlucky_record->total_rateable_value, $before_changes->total_rateable_value + 1337);
        $this->assertEquals($unlucky_record->listed_building, !$before_changes->listed_building);
        $this->assertEquals($unlucky_record->live, !$before_changes->live);
        $this->assertEquals($unlucky_record->conservation_area, !$before_changes->conservation_area);
        $this->assertEquals($unlucky_record->air_conditioned, !$before_changes->air_conditioned);
        $this->assertEquals($unlucky_record->vat_registered, !$before_changes->vat_registered);
        $this->assertEquals($unlucky_record->approved, !$before_changes->approved);
        $this->assertEquals($unlucky_record->approved_initials, $new_user->calculateInitials());
        $this->assertEquals($unlucky_record->held_initials, $new_user->calculateInitials());
        $this->assertEquals(
            $unlucky_record->void_total_rateable_value,
            $before_changes->void_total_rateable_value + 1337
        );
        $this->assertEquals(
            $unlucky_record->total_gross_internal_area,
            $before_changes->total_gross_internal_area + 1337
        );
        $this->assertEquals(
            $unlucky_record->void_total_lettable_area,
            $before_changes->void_total_lettable_area + 1337
        );
    }

    public function testDoesPropertiesControllerValidateProperly()
    {
        $submitted_data = [
            'portfolio_id'              => -1,
            'property_manager_id'       => -1,
            'property_status_id'        => -1,
            'property_use_id'           => -1,
            'property_tenure_id'        => -1,
            'location_type_id'          => -1,
            'property_category_id'      => -1,
            'name'                      => '',
            'yardi_property_ref'        => '',
            'yardi_alt_ref'             => -1,
            'stop_posting_id'           => -1,
            'total_lettable_area'       => 'notAnInteger',
            'void_total_lettable_area'  => 'notAnInteger',
            'total_site_area'           => 'notAnInteger',
            'total_gross_internal_area' => 'notAnInteger',
            'total_rateable_value'      => 'notAnInteger',
            'void_total_rateable_value' => 'notAnInteger',
            'listed_building'           => 'notABoolean',
            'live'                      => 'notABoolean',
            'conservation_area'         => 'notABoolean',
            'air_conditioned'           => 'notABoolean',
            'vat_registered'            => 'notABoolean',
            'approved'                  => 'notABoolean',
            'approved_at'               => 'notAValidDate',
            'approved_initials'         => 919191,
            'held_initials'             => 919191,
            'held_at'                   => 'notAValidDate'
        ];

        // Check if all errors are present
        $this->apiAs(
            $this->dev_user,
            'POST',
            '/api/property/properties/',
            $submitted_data,
            []
        )->assertJsonValidationErrors(array_keys($submitted_data));
    }

    public function testCanUsersDeleteProperties()
    {
        $unlucky_record = (factory(Property::class, 1)->create([
            'portfolio_id'        => Portfolio::first()->id,
            'property_manager_id' => PropertyManager::first()->id
        ]))->first();
        $this->assertInstanceOf(Property::class, $unlucky_record);
        $this->apiAs(
            $this->dev_user,
            'DELETE',
            '/api/property/properties/' . $unlucky_record->id,
            [],
            []
        )->assertSuccessful();
        $this->assertNull(Property::find($unlucky_record->id));
    }

    public function testCanUsersPaginatePropertiesDataTable()
    {
        // Try with two values to avoid the .00001% chance the size of the full table is the attempted page size
        $result = $this->apiAs($this->dev_user, 'GET', '/api/property/properties/data-table', [
            'limit' => 2,
        ], []);
        $result->assertSuccessful();
        $this->assertSame(2, count($result->json()['rows']));

        $result = $this->apiAs($this->dev_user, 'GET', '/api/property/properties/data-table', [
            'limit' => 3,
        ], []);
        $result->assertSuccessful();
        $this->assertSame(3, count($result->json()['rows']));
    }

    public function testCanUsersSortPropertiesDataTable()
    {
        // Try with two values to avoid the .00001% chance the size of the full table is the attempted page size
        $result = $this->apiAs($this->dev_user, 'GET', '/api/property/properties/data-table', [
            'sort_column' => 'id',
            'sort_order'  => 'desc',
            'limit'       => 1
        ], []);
        $result->assertSuccessful();
        $this->assertSame(
            Property::orderBy(Property::getTableName().'.id', 'desc')->first()->id,
            $result->json()['rows'][0]['id']
        );

        $result = $this->apiAs($this->dev_user, 'GET', '/api/property/properties/data-table', [
            'sort_column' => 'id',
            'sort_order'  => 'asc',
            'limit'       => 1
        ], []);
        $result->assertSuccessful();
        $this->assertSame(
            Property::orderBy(Property::getTableName().'.id', 'asc')->first()->id,
            $result->json()['rows'][0]['id']
        );
    }

    public function testCanUsersFilterPropertiesDataTable()
    {
        $filter_clientAccount    = ClientAccount::randomRow();
        $filter_portfolio        = Portfolio::randomRow();
        $filter_property_manager = PropertyManager::randomRow();
        $filter_tenure           = PropertyTenure::randomRow();

        // Check client_account_id filter
        $result = $this->apiAs($this->dev_user, 'GET', '/api/property/properties/data-table', [
            'limit'             => 10,
            'client_account_id' => $filter_clientAccount->id,
        ], []);
        $result->assertSuccessful();
        foreach ($result->json()['rows'] as $item) {
            $this->assertSame($filter_clientAccount->id, $item['portfolio']['client_account_id']);
        }

        // Check property_manager_id filter
        $result = $this->apiAs($this->dev_user, 'GET', '/api/property/properties/data-table', [
            'limit'               => 10,
            'property_manager_id' => $filter_property_manager->id,
        ], []);
        $result->assertSuccessful();
        foreach ($result->json()['rows'] as $item) {
            $this->assertSame($filter_property_manager->id, $item['property_manager_id']);
        }

        // Check portfolio_id filter
        $result = $this->apiAs($this->dev_user, 'GET', '/api/property/properties/data-table', [
            'limit'        => 10,
            'portfolio_id' => $filter_portfolio->id,
        ], []);
        $result->assertSuccessful();
        foreach ($result->json()['rows'] as $item) {
            $this->assertSame($filter_portfolio->id, $item['portfolio_id']);
        }

        // Check tenure_id filter
        $result = $this->apiAs($this->dev_user, 'GET', '/api/property/properties/data-table', [
            'limit'              => 10,
            'property_tenure_id' => $filter_tenure->id,
        ], []);
        $result->assertSuccessful();
        foreach ($result->json()['rows'] as $item) {
            $this->assertSame($filter_tenure->id, $item['property_tenure_id']);
        }

        $result = $this->apiAs($this->dev_user, 'GET', '/api/property/properties/data-table', [
            'property_name_partial'         => 'a',
        ], []);
        $result->assertSuccessful();
        foreach ($result->json()['rows'] as $item) {
            $this->assertContains('a', strtolower($item['name']));
        }
        $result = $this->apiAs($this->dev_user, 'GET', '/api/property/properties/data-table', [
            'property_name_partial'         => 'b',
        ], []);
        $result->assertSuccessful();
        foreach ($result->json()['rows'] as $item) {
            $this->assertContains('b', strtolower($item['name']));
        }
        $result = $this->apiAs($this->dev_user, 'GET', '/api/property/properties/data-table', [
            'property_name_partial'         => 'c',
        ], []);
        $result->assertSuccessful();
        foreach ($result->json()['rows'] as $item) {
            $this->assertContains('c', strtolower($item['name']));
        }
    }

    public function testCanUsersReadPropertiesDataTable()
    {
        $result = $this->apiAs($this->dev_user, 'GET', '/api/property/properties/data-table', [
            'limit' => Property::count(),
        ], []);
        $result->assertSuccessful();

        $result->assertJsonStructure([
            'rows' => [
                '*' => [
                    'id',
                    'address'          => [
                        'county',
                        'country'
                    ],
                    'property_manager' => [
                        'user' => [
                            'role'
                        ]
                    ],
                    'property_tenure'  => [
                        'id', 'name'
                    ],
                    'portfolio'        => [
                        'client_account'
                    ]
                ]
            ],
            'row_count'
        ]);
    }
}
