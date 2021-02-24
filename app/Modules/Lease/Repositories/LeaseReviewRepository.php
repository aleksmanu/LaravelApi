<?php
namespace App\Modules\Lease\Repositories;

use App\Modules\Lease\Models\Lease;
use App\Modules\Lease\Models\LeaseReview;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class LeaseReviewRepository
{
    /**
     * @var LeaseReview
     */
    protected $model;

    /**
     * BreakPartyOptionRepository constructor.
     * @param LeaseReview $model
     */
    public function __construct(LeaseReview $model)
    {
        $this->model = $model;
    }

    public function get(): Collection
    {
        return $this->model->all();
    }

    public function getForType(string $type): Collection
    {
        return $this->model->where('entity_type', $type)->get();
    }

    public function importRecord($leaseId)
    {
        $lease = Lease::find($leaseId);

        if (!$lease->first_review) {
            return;
        }
        $end = Carbon::parse($lease->lease_end);
        $date = Carbon::parse($lease->first_review);
        $frequency = $lease->review_pattern;
        if ($frequency === '0' || is_null($frequency)) {
            return;
        }

        $data = [
            'entity_type' => Lease::class,
            'entity_id' => $lease->id,
            'date' => $date
        ];
        LeaseReview::create($data);

        while ($date <= $end) {
            $date = $date->addYears($frequency);
            if ($date <= $end) {
                $data = [
                    'entity_type' => Lease::class,
                    'entity_id' => $lease->id,
                    'date' => $date
                ];
                LeaseReview::create($data);
            }
        }
    }
}
