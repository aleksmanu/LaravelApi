<?php
namespace App\Modules\Acquisition\Repositories;

use App\Modules\Acquisition\Models\Checklist;
use App\Modules\Acquisition\Models\PopArea;
use App\Modules\Acquisition\Models\Site;
use App\Modules\Attachments\Traits\RepoHasAttachments;
use App\Modules\Core\Classes\Repository;
use App\Modules\Core\Library\MapHelper;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SiteRepository extends Repository
{
    use RepoHasAttachments;

    public function __construct(Site $model)
    {
        $this->model = $model;
    }

    public function showSite($id)
    {
        $site = $this->model->findOrFail($id);
        return $site->load([
            'steps'
        ]);
    }

    public function updateSite($id, array $fields)
    {
        $gpsData = false;
        $site = $this->model->find($id);
        $newPostcode = false;
        // this is so stupid and yet I can't think of a better way
        // because this fucking request is structured as [{k => v}]

        foreach ($fields as $field) {
            if (array_key_exists('value', $field)) {
                if ($field['key'] === 'postcode') {
                    $newPostcode = true;
                    if (!$field['value']) {
                        continue; // ignore empty or null lat/long values. Need 0? tough luck, try 0.000001
                    }
                }

                $site[$field['key']] = $field['value'];
            }
        }

        // Override latitude and longitude if postcode is changes
        if ($newPostcode) {
            $gpsData = MapHelper::geocodePostcode($site->postcode);
        }

        if ($gpsData) {
            $site->latitude = $gpsData['lat'];
            $site->longitude = $gpsData['long'];
        }

        $site->update($site->toArray());
        return $site;
    }

    public function createSite(array $fields)
    {
        DB::transaction(function () use ($fields) {
            $fields = $fields['site'];

            $fromChecklist = Checklist::findOrFail($fields['checklist_id']);
            $popArea = PopArea::findOrFail($fields['pop_area_id']);
            $acquisition = $popArea->bareAcquisition;
            unset(
                $fields['checklist_id'],
                $fields['acquisition_id']
            );
            $fields['status'] = 'active';
        

            $site = new Site($fields);
            $gpsData = MapHelper::geocodePostcode($site->postcode);
            if ($gpsData) {
                $site->latitude = $gpsData['lat'];
                $site->longitude = $gpsData['long'];
            }
            $popArea->sites()->save($site);

            $stepCollection = [];

            $newChecklist = $fromChecklist->replicate();
            $newChecklist->acquisition_site_id = $site->id;
            $newChecklist->is_template = false;
            $newChecklist->save();

            $stepCollection = [];

            foreach ($fromChecklist->steps as $step) {
                $newStep = $step->replicate();
                $newStep->acquisition_checklist_id = $newChecklist->id;
                $newStep->completed_on = null;

                $currDate = new Carbon($acquisition->commence_at);
               
                if ($newStep['depends_on_step_order_number'] === null) {
                    $newStep['start_on'] = new Carbon($acquisition->commence_at);
                    $newStep['target_date'] = $currDate->addDays($newStep['duration_days']);
                    $newStep['forecast_for'] = $newStep['target_date'];
                    if ($newStep['label'] === 'MS1 - Instructions Issued') {
                        $newStep['completed_on'] = $newStep['start_on'];
                    }
                } else {
                    $dependsOnStepIndex = $newStep['depends_on_step_order_number'] - 1;
                    $dependsOnStepForecast = $stepCollection[$dependsOnStepIndex]->forecast_for;
                    $newStep['start_on'] = new Carbon($dependsOnStepForecast);
                    $newStep['target_date'] = (new Carbon($dependsOnStepForecast))->addDays($newStep['duration_days']);
                    $newStep['forecast_for'] = $newStep['target_date'];
                }
                $newStep->save();
                $stepCollection[] = $newStep;
            }

            return $site;
        });
    }
}
