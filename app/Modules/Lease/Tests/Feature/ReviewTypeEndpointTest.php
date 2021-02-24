<?php
namespace App\Modules\Lease\Tests\Feature;

use App\Modules\Common\Classes\EndpointTest;
use App\Modules\Common\Traits\TestsEndpoints;
use App\Modules\Lease\Models\ReviewType;
use Tests\TestCase;

class ReviewTypeEndpointTest extends EndpointTest
{
    public function testCansersIndexReviewTypes()
    {
        $this->assertGetCountForAllUserTypes('/api/lease/review-types/', ReviewType::query());
    }

    public function testCanUsersReadReviewTypes()
    {
        $existing_id = ReviewType::first()->id;
        $this->assertGetForAllUserTypes('/api/lease/review-types/' . $existing_id);
        $this->apiAs($this->dev_user, 'GET', '/api/lease/review-types/-1', [], [])->assertStatus(404);
    }

    public function testCanUsersCreateReviewTypes()
    {
        $this->apiAs($this->dev_user, 'POST', '/api/lease/review-types/', [
            'name' => 'GottaMakeItImpossibleToCollideAmirite'
        ], [])->assertSuccessful();

        $this->assertInstanceOf(
            ReviewType::class,
            ReviewType::where('name', 'GottaMakeItImpossibleToCollideAmirite')->first()
        );
    }

    public function testCanUsersUpdateReviewTypes()
    {
        // Select a random record
        $unlucky_record = ReviewType::randomRow();
        $before_changes = clone $unlucky_record;

        // Make a patch request
        $this->apiAs($this->dev_user, 'PATCH', '/api/lease/review-types/' . $unlucky_record->id, [
            'name' => 'ThisIsAPrefix' . $unlucky_record->name,
        ], [])->assertSuccessful();

        // Check if changes happened
        $unlucky_record = $unlucky_record->fresh();
        $this->assertEquals($unlucky_record->name, 'ThisIsAPrefix' . $before_changes->name);
    }

    public function testDoesReviewTypesControllerValidateProperly()
    {
        // Empty name will fail
        $this->apiAs($this->dev_user, 'POST', '/api/lease/review-types/', [
            'name' => '',
        ], [])->assertJsonValidationErrors([
            'name'
        ]);

        // Proper name will succeed
        $this->apiAs($this->dev_user, 'POST', '/api/lease/review-types/', [
            'name' => 'ThisWillDefinitelyBeADuplicate',
        ], [])->assertSuccessful();

        // Same name as before will fail as a duplicate
        $this->apiAs($this->dev_user, 'POST', '/api/lease/review-types/', [
            'name' => 'ThisWillDefinitelyBeADuplicate',
        ], [])->assertJsonValidationErrors([
            'name'
        ]);
    }

    public function testCanUsersDeleteReviewTypes()
    {
        $unlucky_record = (factory(ReviewType::class, 1)->create())->first();
        $this->assertInstanceOf(ReviewType::class, $unlucky_record);
        $this->apiAs(
            $this->dev_user,
            'DELETE',
            '/api/lease/review-types/' . $unlucky_record->id,
            [],
            []
        )->assertSuccessful();
        $this->assertNull(ReviewType::find($unlucky_record->id));
    }
}
