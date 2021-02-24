<?php
namespace App\Modules\Common\Tests\Feature;

use App\Modules\Common\Classes\EndpointTest;

class DropdownAggregatorTest extends EndpointTest
{
    public function testCanUserGetchDropdowns()
    {
        $result = $this->apiAs($this->dev_user, 'POST', '/api/common/dropdown-aggregator/fetch', [
            'account_id' => '',
            'client_account_status_id' => '',
            'client_account_id' => '',
            'property_manager_id' => '',
            'portfolio_id' => '',
            'property_id' => '',
            'address_id' => '',
            'organisation_type_id' => '',
            'county_id' => '',
            'country_id' => '',
            'property_status_id' => '',
            'property_use_id' => '',
            'property_tenure_id' => '',
            'location_type_id' => '',
            'property_category_id' => '',
            'stop_posting_id' => '',
            'measurement_unit_id' => '',
        ], []);
        $result->assertSuccessful();

        $result->assertJsonStructure([
            'accounts',
            'client_account_statuses',
            'client_accounts',
            'property_managers',
            'portfolios',
            'properties',
            'addresses',
            'organisation_types',
            'counties',
            'countries',
            'property_statuses',
            'property_uses',
            'property_tenures',
            'location_types',
            'property_categories',
            'stop_postings',
            'measurement_units',
        ]);
    }
}
