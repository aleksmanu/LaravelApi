<?php
namespace App\Modules\Edits\Repositories;

use App\Modules\Edits\Models\ReviewStatus;
use Illuminate\Support\Collection;

class ReviewStatusRepository
{

    /**
     * @var ReviewStatus
     */
    protected $model;

    /**
     * ReviewStatusRepository constructor.
     * @param ReviewStatus $model
     */
    public function __construct(ReviewStatus $model)
    {
        $this->model = $model;
    }

    /**
     * @param string $entity_table
     * @return Collection
     */
    public function getReviewStatusSplitByEntityType(string $entity_table): Collection
    {

        $select = "count($entity_table.id) as value,
                   review_statuses.name as name";

        return $this->model->selectRaw($select)
                           ->join($entity_table, "$entity_table.review_status_id", '=', 'review_statuses.id')
                           ->groupBy('review_statuses.id')
                           ->get();
    }
}
