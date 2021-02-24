<?php

namespace App\Modules\Lease\Tests\Feature;

use App\Modules\Common\Classes\EndpointTest;
use App\Modules\Common\Traits\TestsCardSummaryData;
use App\Modules\Lease\Models\BreakPartyOption;
use App\Modules\Lease\Models\Lease;
use App\Modules\Lease\Models\LeaseType;
use App\Modules\Lease\Models\RentFrequency;
use App\Modules\Lease\Models\ReviewType;
use App\Modules\Property\Models\Unit;

class LeaseEndpointTest extends EndpointTest
{
    use TestsCardSummaryData;

    public function testCanUsersIndexLeases()
    {
        $this->assertGetCountForAllUserTypes('/api/lease/leases/', Lease::query());
    }

    public function testCanUsersReadLeases()
    {
        $existing_id = Lease::first()->id;
        $this->assertGetForAllUserTypes('/api/lease/leases/' . $existing_id);
        $this->apiAs($this->dev_user, 'GET', '/api/lease/leases/-1', [], [])->assertStatus(404);
    }

    public function testDoesClientAccountControllerValidateProperly()
    {
        $submitted_data = [
            'annual_rent_vat_rate'           => 'notAnInteger',
            'annual_service_charge_vat_rate' => 'notAnInteger',
            'lease_type_id'                  => -1,
            'break_party_option_id'          => -1,
            'break_notice_days'              => 'notAnInteger',
            'annual_rent'                    => 'notAFloat',
            'annual_service_charge'          => 'notAFloat',
            'live'                           => 'notABoolean',
            'next_break_at'                  => 'notADate',
            'next_review_at'                 => 'notADate',
            'expiry_at'                      => 'notADate',
            'commencement_at'                => 'notADate',
        ];

        // Check if all errors are present
        $this->apiAs(
            $this->authoriser_user,
            'POST',
            '/api/lease/leases/',
            $submitted_data,
            []
        )->assertJsonValidationErrors(array_keys($submitted_data));
    }
}
