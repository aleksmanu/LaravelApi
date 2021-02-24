<?php
namespace App\Modules\Acquisition\Http\Controllers;

use App\Modules\Acquisition\Models\Acquisition;
use App\Modules\Acquisition\Models\Step;
use App\Modules\Acquisition\Models\Site;

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

        return response()->json([
            'sites_count' => $this->sites()->count(),
            'tasks_count' => $this->tasks()->count(),
            'overdue_count' => $this->overdue()->count(),
            'my_overdue_count' => $this->myOverdue()->count(),
            'acquisitions' => $this->getAllAcquisitions(),
        ]);
    }

    public function getStatistic(string $context) {
        switch ($context) {
            case 'overdue':
                return $this->groupOverdue();
                break;
            default:
                return abort(404);
                break;
        }
    }
  
    /**
     * List of work order statistics
     *
     * @return \Illuminate\Http\Response
     */
    public function tasksIndex()
    {
        return response()->json([
            'tasks' => $this->tasks()
                ->select(['acquisition_steps.*', 'acquisition_acquisitions.id as acquisition_id'])
                ->get()
        ]);
    }

    private function sites()
    {
        return Site::withCount('overdueSteps');
    }
    
    private function tasks()
    {
        return Step::
            leftJoin(
                'acquisition_checklists',
                'acquisition_checklists.id',
                '=',
                'acquisition_steps.acquisition_checklist_id'
            )->leftJoin('acquisition_sites', 'acquisition_sites.id', '=', 'acquisition_checklists.acquisition_site_id')
            ->leftJoin('acquisition_pop_areas', 'acquisition_pop_areas.id', '=', 'acquisition_sites.pop_area_id')
            ->leftJoin(
                'acquisition_acquisitions',
                'acquisition_acquisitions.id',
                '=',
                'acquisition_pop_areas.acquisition_id'
            )->where('acquisition_steps.role_id', request()->user()->role->id)
            ->whereNull('acquisition_steps.completed_on')
            ->whereIn('acquisition_pop_areas.acquisition_id', request()->user()->acquisitions->pluck('id'))
            ->select('acquisition_steps.*');
    }
    
    private function overdue()
    {
        return Step::whereNull('completed_on')
            ->whereDate('forecast_for', '<', Carbon::now());
    }

    private function groupOverdue()
    {
        $toReturn = [
            'data'  => [],
            'acquisitions' => [],
        ];

        $overdue = $this->overdue()->get();
        foreach ($overdue->toArray() as $task) {
            $acquisiton = $task['minimal_context']['acquisition']['name'];
            $toReturn['acquisitions'][$acquisiton]['steps'][] = $task;
        }

        foreach ($toReturn['acquisitions'] as $i => $data) {
            $toReturn['data'][] = [
                'name' => $i,
                'steps' => $data['steps'],
            ];
        }
        unset($toReturn['acquisitions']);
        return $toReturn['data'];
    }

    private function myOverdue()
    {
        return $this->tasks()
            ->whereDate('forecast_for', '<', Carbon::now());
    }

    private function getSitesByStatusArray($context)
    {
        $data = Acquisition::with(["popAreas.$context"])->get()->toArray();
        foreach ($data as $ai => $acquisiton) {
            foreach ($acquisiton['pop_areas'] as $pi => $pop) {
                if (empty($pop[$context])) {
                    unset($data[$ai]['pop_areas'][$pi]);
                }
            }
            if (empty($data[$ai]['pop_areas'])) {
                unset($data[$ai]);
            } else {
                $data[$ai]['pop_areas'] = array_values($data[$ai]['pop_areas']);
            }
        }

        return array_values($data);
        // return Acquisition::with(["popAreas.complete", "popAreas.active", "popAreas.cancelled"])->get()->toArray();
    }


    private function getAllAcquisitions()
    {
        return Acquisition::with(['popAreas.sites'])->get()->toArray();
    }
}
