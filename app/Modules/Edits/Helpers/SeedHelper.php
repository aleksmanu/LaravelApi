<?php
namespace App\Modules\Edits\Helpers;

use App\Modules\Auth\Models\User;
use App\Modules\Core\Library\DatabaseHelper;
use App\Modules\Core\Library\EloquentHelper;
use App\Modules\Edits\Models\EditStatus;
use App\Modules\Edits\Models\ReviewStatus;
use Carbon\Carbon;

class SeedHelper
{
    /**
     * Randomly set review
     * @param array $data
     */
    public static function setRandomReviewStatus(array &$data)
    {

        if (rand(0, 1)) {
            $data['locked_at']         = Carbon::now();
            $data['locked_by_user_id'] = User::inRandomOrder()->first()->id;

            $review_status = EloquentHelper::getRecordIdBySlug(ReviewStatus::class, ReviewStatus::IN_REVIEW);
        } else {
            $review_status = ReviewStatus::where('slug', '!=', ReviewStatus::IN_REVIEW)->inRandomOrder()->first()->id;
        }
        $data['review_status_id'] = $review_status;
    }

    public static function setRandomEditStatus(array &$data, $completed)
    {

        if ($completed) {
            $data['edit_status_id'] = EditStatus::where(
                'slug',
                '!=',
                EditStatus::PENDING
            )->inRandomOrder()->first()->id;
        } else {
            $data['edit_status_id'] = EloquentHelper::getRecordIdBySlug(EditStatus::class, EditStatus::PENDING);
        }
    }

    public static function getValueBasedOnFieldType($table, $field, $faker)
    {
        $type = DatabaseHelper::getFieldType($table, $field);

        //TODO: Create logic to handle database types and foreign keys
        // if($type === 'integer'){
        //     return $faker->randomNumber();
        // } else {
        //     return $faker->word;
        // }
        return 1;
    }

    /**
     * @param $model
     * @param $max
     * @param $review_status_slug
     * @return mixed
     */
    public static function getSampleEntitySet($model, $max, $review_status_slug)
    {

        $model         = \App::make($model);
        $review_status = EloquentHelper::getRecordIdBySlug(ReviewStatus::class, $review_status_slug);

        return $model->query()->inRandomOrder()->take($max)->where('review_status_id', $review_status)->get();
    }

    /**
     * @param $data
     * @return array
     */
    public static function removeUtilityFields($data)
    {
        return array_diff(
            $data,
            [
                'id',
                'locked_by_user_id',
                'review_status_id',
                'locked_at',
                'created_at',
                'updated_at',
                'deleted_at'
            ]
        );
    }
}
