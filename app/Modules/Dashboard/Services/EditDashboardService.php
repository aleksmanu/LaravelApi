<?php

namespace App\Modules\Dashboard\Services;

use App\Modules\Core\Library\ChartLibrary\BarChartParser;
use App\Modules\Core\Library\ChartLibrary\LineChartParser;
use App\Modules\Edits\Repositories\EditBatchRepository;
use App\Modules\Edits\Repositories\EditRepository;
use App\Modules\Edits\Repositories\ReviewStatusRepository;
use Carbon\Carbon;

class EditDashboardService
{

    /**
     * @var ReviewStatusRepository
     */
    protected $review_status_repository;

    /**
     * @var EditRepository
     */
    protected $edits_repository;

    /**
     * @var EditBatchRepository
     */
    protected $edit_batch_repository;

    /**
     * EditDashboardService constructor.
     * @param ReviewStatusRepository $review_status_repository
     * @param EditRepository $edits_repository
     * @param EditBatchRepository $edit_batch_repository
     */
    public function __construct(
        ReviewStatusRepository $review_status_repository,
        EditRepository $edits_repository,
        EditBatchRepository $edit_batch_repository
    ) {
        $this->review_status_repository = $review_status_repository;
        $this->edits_repository         = $edits_repository;
        $this->edit_batch_repository    = $edit_batch_repository;
    }

    /**
     * @param $entity_table
     * @return array
     */
    public function getReviewStatusByEntityType($entity_table): array
    {

        $data = $this->review_status_repository->getReviewStatusSplitByEntityType($entity_table);
        $data = $data->toArray();

        //Calculate total for entity
        $total = 0;
        foreach ($data as &$datum) {
            $total += $datum['value'];
        }
        array_unshift($data, ['value' => $total, 'name' => 'Total']); //Total should be placed at the start
        return $data;
    }

    /**
     * @return array
     */
    public function getDailyEditsApproval(): array
    {

        $end_date   = Carbon::now()->subDay()->endOfDay();
        $start_date = $end_date->copy()->subDays(6)->startOfDay();

        $data = $this->edits_repository->getApprovedEditsBetweenDatesByStatus($start_date, $end_date);
        $data = $data->groupBy('name')->toArray();

        $line_chart_parser = \App::make(LineChartParser::class);

        return $line_chart_parser->load($data)
                                 ->setDatesBetweenSeries($start_date, $end_date, 'date')
                                 ->sortSeriesByKey('date')
                                 ->get('date');
    }

    /**
     * @return array
     */
    public function getRejectedAcceptedEditSplit(): array
    {

        $data = $this->edits_repository->getEditsApprovalSplit();
        $data = $data->toArray();

        $bar_chart_parser = \App::make(BarChartParser::class);
        return $bar_chart_parser->load($data)
                                ->setSeriesType(BarChartParser::STANDARD)
                                ->get();
    }

    /**
     * @return array
     */
    public function getReviewedEditsTotal(): array
    {

        $data = $this->edits_repository->getReviewedEditsTotal();
        return [
                    ['name' => 'Total', 'value' => $data['total']],
                    ['name' => 'Reviewed', 'value' => $data['reviewed']]
        ];
    }
}
