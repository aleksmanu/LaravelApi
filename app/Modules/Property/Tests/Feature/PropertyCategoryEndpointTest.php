<?php
namespace App\Modules\Property\Tests\Feature;

use App\Modules\Common\Classes\EndpointTest;
use App\Modules\Property\Models\PropertyCategory;

class PropertyCategoryEndpointTest extends EndpointTest
{
    public function testCanUsersIndexPropertyCategories()
    {
        $this->assertGetCountForAllUserTypes('/api/property/property-categories/', PropertyCategory::query());
    }

    public function testCanUsersReadPropertyCategories()
    {
        $existing_id = PropertyCategory::first()->id;
        $this->assertGetForAllUserTypes('/api/property/property-categories/' . $existing_id);
        $this->apiAs($this->dev_user, 'GET', '/api/property/property-categories/-1', [], [])->assertStatus(404);
    }

    public function testCanUsersCreatePropertyCategories()
    {
        $this->apiAs($this->dev_user, 'POST', '/api/property/property-categories/', [
            'name' => 'GottaMakeItImpossibleToCollideAmirite',
        ], [])->assertSuccessful();

        $this->assertInstanceOf(
            PropertyCategory::class,
            PropertyCategory::where('name', 'GottaMakeItImpossibleToCollideAmirite')->first()
        );
    }

    public function testCanUsersUpdatePropertyCategories()
    {
        // Select a random record
        $unlucky_record = PropertyCategory::randomRow();
        $before_changes = clone $unlucky_record;

        // Make a patch request
        $this->apiAs($this->dev_user, 'PATCH', '/api/property/property-categories/' . $unlucky_record->id, [
            'name' => 'ThisIsAPrefix' . $unlucky_record->name,
        ], [])->assertSuccessful();

        // Check if changes happened
        $unlucky_record = $unlucky_record->fresh();
        $this->assertEquals($unlucky_record->name, 'ThisIsAPrefix' . $before_changes->name);
    }

    public function testDoesPropertyCategoriesControllerValidateProperly()
    {
        // Empty name will fail
        $this->apiAs($this->dev_user, 'POST', '/api/property/property-categories/', [
            'name' => '',
        ], [])->assertJsonValidationErrors([
            'name',
        ]);

        // Proper name will succeed
        $this->apiAs($this->dev_user, 'POST', '/api/property/property-categories/', [
            'name' => 'ThisWillDefinitelyBeADuplicate',
        ], [])->assertSuccessful();

        // Same name as before will fail as a duplicate
        $this->apiAs($this->dev_user, 'POST', '/api/property/property-categories/', [
            'name' => 'ThisWillDefinitelyBeADuplicate',
        ], [])->assertJsonValidationErrors([
            'name'
        ]);
    }

    public function testCanUsersDeletePropertyCategories()
    {
        $unlucky_record = PropertyCategory::create([
            'name' => 'test_imperial_toothwidths',
            'slug' => 'slimy-and-not-salty'
        ]);
        $this->assertInstanceOf(PropertyCategory::class, $unlucky_record);
        $this->apiAs(
            $this->dev_user,
            'DELETE',
            '/api/property/property-categories/' . $unlucky_record->id,
            [],
            []
        )->assertSuccessful();
        $this->assertNull(PropertyCategory::find($unlucky_record->id));
    }
}
