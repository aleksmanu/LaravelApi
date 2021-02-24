<?php

namespace App\Modules\Common\Tests\Feature;

use App\Modules\Common\Classes\EndpointTest;
use App\Modules\Common\Models\County;

class CountyEndpointTest extends EndpointTest
{
    public function testCanUsersIndexCounties()
    {
        $this->assertGetCountForAllUserTypes('/api/common/counties/', County::query());
    }

    public function testCanUsersReadCounties()
    {
        $existing_id = County::first()->id;
        $this->assertGetForAllUserTypes('/api/common/counties/' . $existing_id);
        $this->apiAs($this->dev_user, 'GET', '/api/common/counties/-1', [], [])->assertStatus(404);
    }

    public function testCanUsersCreateCounties()
    {
        $this->apiAs($this->dev_user, 'POST', '/api/common/counties/', [
            'name' => 'GottaMakeItImpossibleToCollideAmirite',
        ], [])->assertSuccessful();

        $this->assertInstanceOf(County::class, County::where('name', 'GottaMakeItImpossibleToCollideAmirite')->first());
    }

    public function testCanUsersUpdateCounties()
    {
        // Select a random record
        $unlucky_record = County::randomRow();
        $before_changes = clone $unlucky_record;

        // Make a patch request
        $this->apiAs($this->dev_user, 'PATCH', '/api/common/counties/' . $unlucky_record->id, [
            'name' => 'ThisIsAPrefix' . $unlucky_record->name,
        ], [])->assertSuccessful();

        // Check if changes happened
        $unlucky_record = $unlucky_record->fresh();
        $this->assertEquals($unlucky_record->name, 'ThisIsAPrefix' . $before_changes->name);
    }

    public function testDoesCountiesControllerValidateProperly()
    {
        // Empty name will fail
        $this->apiAs($this->dev_user, 'POST', '/api/common/counties/', [
            'name' => '',
            'region_id' => -1
        ], [])->assertJsonValidationErrors([
            'name'
        ]);
    }

    public function testCanUsersDeleteCounties()
    {
        $unlucky_record = (factory(County::class, 1)->create())->first();
        $this->assertInstanceOf(County::class, $unlucky_record);
        $this->apiAs(
            $this->dev_user,
            'DELETE',
            '/api/common/counties/' . $unlucky_record->id,
            [],
            []
        )->assertSuccessful();
        $this->assertNull(County::find($unlucky_record->id));
    }
}
