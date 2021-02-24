<?php

namespace App\Modules\Property\Tests\Unit\Controllers\PropertyController;

use App\Modules\Property\Http\Controllers\PropertyController;
use App\Modules\Property\Models\Property;
use App\Modules\TestFramework\Classes\BaseTestClass;

class GetEditableTest extends BaseTestClass
{

    /**
     * @see PropertyController::getEditable()
     * @test
     * @throws \Exception
     */
    public function test()
    {

        $result = $this->apiAs($this->user, 'GET', '/api/property/properties/get-editable', []);
        $result->assertSuccessful();

        $result = $result->json();

        $model = new Property();
        foreach ($model->getEditable() as $k => $v) {
            $this->assertSame($v, $result[$k]);
        }
    }


    public function testUpdate()
    {

        $this->markTestSkipped('Update goes thru edit batch');

        $result = $this->apiAs($this->user, 'PUT', '/api/property/properties/1', [
            "portfolio_id"              => 1,
            "property_manager_id"       => 1,
            "property_status_id"        => 1,
            "property_use_id"           => 1,
            "property_tenure_id"        => 9,
            "location_type_id"          => 2,
            "property_category_id"      => 1,
            "stop_posting_id"           => 1,
            "name"                      => "dolores architecto fuga autem ad",
            "yardi_property_ref"        => "72h5i4l",
            "yardi_alt_ref"             => "07t6a5b",
            "total_lettable_area"       => "329939.00",
            "void_total_lettable_area"  => "216857.50",
            "total_site_area"           => "787409.26",
            "total_gross_internal_area" => "3195.95",
            "total_rateable_value"      => "373093.69",
            "void_total_rateable_value" => "945807.74",
            "listed_building"           => 0,
            "live"                      => 1,
            "conservation_area"         => 1,
            "air_conditioned"           => 1,
            "vat_registered"            => 0,
            "approved"                  => 1,
            "approved_at"               => "1986-10-04",
            "approved_initials"         => "GB",
            "held_at"                   => "1983-01-25",
            "held_initials"             => "GB",
            "addr_unit"                 => "Suite 082",
            "addr_number"               => "151",
            "addr_building"             => "velit Building",
            "addr_street"               => "Mikel Cliff",
            "addr_estate"               => "dolores",
            "addr_suburb"               => "fugit",
            "addr_town"                 => "South Ethabury",
            "addr_postcode"             => "79446",
            "county_id"                 => 7,
            "country_id"                => 4,
        ]);
    }
}
