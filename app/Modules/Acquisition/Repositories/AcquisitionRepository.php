<?php
namespace App\Modules\Acquisition\Repositories;

use App\Modules\Common\Classes\AddressDataHelper;

use App\Modules\Acquisition\Models\Acquisition;
use App\Modules\Acquisition\Models\Checklist;
use App\Modules\Acquisition\Models\PopArea;
use App\Modules\Acquisition\Models\Site;
use App\Modules\Acquisition\Models\Step;
use App\Modules\Core\Library\MapHelper;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AcquisitionRepository
{
    public function __construct(Site $siteModel, Step $stepModel, Checklist $checklistModel, Acquisition $model)
    {
        $this->model = $model;
        $this->stepModel = $stepModel;
        $this->siteModel = $siteModel;
        $this->checklistModel = $checklistModel;
    }

    public function create(array $acquisitionData)
    {
        DB::beginTransaction();
        try {
            $acquisition = $this->model->create($acquisitionData);
            $acquisition->users()->attach($acquisitionData['users']);

            $siteCollection = [];
            $popAreaCollection = [];

            foreach ($acquisitionData['popAreas'] as $popArea) {
                $popArea['acquisition_id'] = $acquisition->id;
                $popAreaCollection[] = PopArea::create($popArea);
            }
            
            foreach ($acquisitionData['sites'] as $siteData) {
                $checklistData = $siteData['checklist'];
                unset(
                    $siteData['checklist']['id'],
                    $siteData['checklist']['created_at'],
                    $siteData['checklist']['modified_at']
                );
                $checklistData['is_template'] = false;
                $siteData['status'] = 'active';
                
                $stepCollection = [];
                $allSteps = $siteData['checklist']['steps'];
                foreach ($allSteps as $step) {
                    unset(
                        $step['id'],
                        $step['created_at'],
                        $step['modified_at']
                    );
                    $currDate = new Carbon($acquisition->commence_at);

                    if ($step['depends_on_step_order_number'] === null) {
                        $step['start_on'] = new Carbon($acquisition->commence_at);
                        $step['target_date'] = $currDate->addDays($step['duration_days']);
                        $step['forecast_for'] = $step['target_date'];
                        if ($step['label'] === 'MS1 - Instructions Issued') {
                            $step['completed_on'] = $step['start_on'];
                        }
                    } else {
                        $dependsOnStepIndex = $step['depends_on_step_order_number'] - 1;
                        $dependsOnStepForecast = $stepCollection[$dependsOnStepIndex]->forecast_for;
                        $step['start_on'] = new Carbon($dependsOnStepForecast);
                        $step['target_date'] = (new Carbon($dependsOnStepForecast))->addDays($step['duration_days']);
                        $step['forecast_for'] = $step['target_date'];
                    }
                    
                    $step = new Step($step);
                    $stepCollection[] = $step;
                }
                unset($checklistData['steps']);
                $checklist = Checklist::create($checklistData);
                $checklist->steps()->saveMany($stepCollection);
                $checklist->save();

                if (!array_key_exists('latitude', $siteData) && !array_key_exists('longitude', $siteData)) {
                    AddressDataHelper::setGpsData($siteData);
                }

                $siteData['pop_area_id'] = $popAreaCollection[$siteData['popArea']];
                $site = $popAreaCollection[$siteData['popArea']]->sites()->create($siteData);
                $checklist['acquisition_site_id'] = $site['id'];
                $checklist->save();
                $siteCollection[] = $site;
            }

            $acquisition->popAreas()->saveMany($popAreaCollection);
            $acquisition->push();
        } catch (\Exception $e) {
            DB::rollback();
            return $e;
        }
        DB::commit();
        return $acquisition;
    }

    public function get($id)
    {
        $acquisition = $this->model->findOrFail($id);
        $acquisition->load([
            'sites.checklist.steps.stepGroup',
            'users'
        ]);
        return $acquisition;
    }

    public function deletePopArea($id)
    {
        $popArea = PopArea::findOrFail($id);
        $acquisitionId = $popArea->acquisition->id;
        if ($popArea->sites->count() > 0) {
            return false;
        } else {
            $popArea->delete();
            return $this->get($acquisitionId);
        }
    }

    public function update($acquisition, $data)
    {
        $acquisition = $this->model->findOrFail($acquisition);
        if (array_key_exists('users', $data)) {
            $acquisition->users()->detach();
            foreach ($data['users'] as $user) {
                $acquisition->users()->attach($user['id']);
            }
        }
        if (array_key_exists('sites', $data)) {
            foreach ($data['sites'] as $siteData) {
                $site = Site::findOrFail($siteData['id']);
                $site->update($siteData);
            }
        }
        if (array_key_exists('pop_areas', $data)) {
            foreach ($data['pop_areas'] as $popAreaData) {
                if (!array_key_exists('id', $popAreaData)) {
                    $popAreaData['acquisition_id'] = $acquisition->id;
                    $popArea = PopArea::create($popAreaData);
                    $popArea = $popArea->refresh();
                    $popArea->acquisition()->associate($acquisition);
                }
            }
        }
        $acquisition->update($data);
        return $acquisition->fresh();
    }

    public function updateStep($checklist, $step, $data)
    {
        DB::transaction(function () use ($checklist, $step, $data) {
            $checklist = Checklist::findOrFail($checklist);
            $steps = $checklist->steps();
            $steps->findOrFail($step)->update($data);
            $steps->refresh();

            foreach ($steps as $st) {
                if ($st->completed_on || $st->depends_on_step_order_number === null) {
                    continue;
                }

                $dependsOnStep = $steps->where('order', '=', $st->depends_on_step_order_number)->get()[0];

                if ($dependsOnStep->completed_on) {
                    $st->starts_on = new Carbon($dependsOnStep->completed_on);
                } else {
                    $st->starts_on = new Carbon($dependsOnStep->forecast_for);
                }
                $st->forecast_for = (clone $st->starts_on)->addDays($st->duration_days);

                $st->save();
            }
        });
    }

    public function search($searchTerm)
    {
        return PopArea::select([
                "id",
                "name",
                "slug",
                "created_at",
                "updated_at",
                "acquisition_id",
            ])->where('slug', 'LIKE', "%{$searchTerm}%")
            ->orWhere('name', 'LIKE', "%{$searchTerm}%")
            ->with(['acquisition', 'sites'])
            ->get();
    }

    public function addressSearch($searchTerm)
    {
        return Site::select([
            "id",
            "reference",
            "street",
            "town",
            "postcode",
            "pop_area_id",
        ])
        ->where('street', 'LIKE', "%{$searchTerm}%")
        ->orWhere('town', 'LIKE', "%{$searchTerm}%")
        ->orWhere('postcode', 'LIKE', "%{$searchTerm}%")
        ->with([
            'popArea:id,name,acquisition_id',
            'popArea.acquisition:id,name'
        ])
        ->get();
    }

    public function surveyorSearch($searchTerm)
    {
        return Site::select([
            "id",
            "reference",
            "surveyor_name",
            "pop_area_id",
        ])
        ->where('surveyor_name', 'LIKE', "%{$searchTerm}%")
        ->with([
            'popArea:id,name,acquisition_id',
            'popArea.acquisition:id,name'
        ])->get();
    }

    public function landlordSearch($searchTerm)
    {
        return Site::select([
            "id",
            "reference",
            "landlord_name",
            "pop_area_id",
        ])
        ->where('landlord_name', 'LIKE', "%{$searchTerm}%")
        ->with([
            'popArea:id,name,acquisition_id',
            'popArea.acquisition:id,name'
        ])->get();
    }

    public function referenceSearch($searchTerm)
    {
        return Site::select([
            "id",
            "reference",
            "pop_area_id",
        ])
        ->with(['popArea.acquisition'])
        ->where('reference', 'LIKE', "%{$searchTerm}%")
        ->get();
    }

    public function citySearch($searchTerm)
    {
        return Acquisition::select([
            "id",
            "name",
        ])
        ->where('name', 'LIKE', "%{$searchTerm}%")
        ->get();
    }

    public function appNumberSearch($searchTerm)
    {
        return Site::select([
            "id",
            "reference",
            "planning_application_number",
            "pop_area_id",
        ])
        ->where('planning_application_number', 'LIKE', "%{$searchTerm}%")
        ->with([
            'popArea:id,name,acquisition_id',
            'popArea.acquisition:id,name'
        ])
        ->get();
    }

    public function appTypeSearch($searchTerm)
    {
        return Site::select([
            "id",
            "reference",
            "planning_type",
            "pop_area_id",
        ])
        ->where('planning_type', 'LIKE', "%{$searchTerm}%")
        ->with([
            'popArea:id,name,acquisition_id',
            'popArea.acquisition:id,name'
        ])
        ->get();
    }
}
