<?php
namespace App\Modules\Workorder\Http\Controllers;

use App\Modules\Workorder\Models\Quote;
use App\Modules\Workorder\Models\WorkOrder;
use Carbon\Carbon;

use App\Http\Controllers\Controller;

class StatisticsController extends Controller
{
    public function __construct()
    {
    }

    /**
     * List of work order statistics
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $outstanding = $this->outstanding();
        $requested = $this->requested();
        $in_progress = $this->inProgress();
        $recently_completed = $this->recentlyCompleted();
        /*
            QUOTES OUTSTANDING - QUOTES NOT ACCEPTED OR REJECTED
            WOS REQUESTED - WORK ORDER WITH A FUTURE DUE DATE
            IN PROGRESS - WORK ORDER NOT COMPLETED DUE TODAY OR BEFORE
            RECENTLY COMPLETED - ANYTHING COMPLETED IN THE LAST 7 DAYS
        */
        return response()->json([
            'outstanding'              => $outstanding,
            'outstanding_count'        => $outstanding->count(),
            'requested'                => $requested,
            'requested_count'          => $requested->count(),
            'in_progress'              => $in_progress,
            'in_progress_count'        => $in_progress->count(),
            'recently_completed'       => $recently_completed,
            'recently_completed_count' => $recently_completed->count(),
        ]);
    }

    private function outstanding()
    {
        return Quote::select(
            Quote::getTableName() . '.*'
        )->whereNull('locked_by_id')->get();
    }

    private function requested()
    {
        return Quote::select(
            Quote::getTableName() . '.*'
        )->where(
            'due_at',
            '>',
            Carbon::now()
        )->join(
            WorkOrder::getTableName(),
            Quote::getTableName() . '.id',
            '=',
            WorkOrder::getTableName() . '.quote_id'
        )->whereNull(
            WorkOrder::getTableName() . '.completed_by_id'
        )->get();
    }

    private function inProgress()
    {
        return Quote::select(
            Quote::getTableName() . '.*'
        )->where(
            'due_at',
            '<=',
            Carbon::now()
        )->join(
            WorkOrder::getTableName(),
            Quote::getTableName() . '.id',
            '=',
            WorkOrder::getTableName() . '.quote_id'
        )->whereNull(
            WorkOrder::getTableName() . '.completed_by_id'
        )->get();
    }

    private function recentlyCompleted()
    {
        return Quote::select(
            Quote::getTableName() . '.*'
        )->join(
            WorkOrder::getTableName(),
            Quote::getTableName() . '.id',
            '=',
            WorkOrder::getTableName() . '.quote_id'
        )->whereBetween(
            'completed_at',
            [Carbon::today()->subDays(7)->startOfDay(), Carbon::tomorrow()->startOfDay()]
        )->get();
    }
}
