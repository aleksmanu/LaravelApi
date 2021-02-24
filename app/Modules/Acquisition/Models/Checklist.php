<?php

namespace App\Modules\Acquisition\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Checklist extends Model
{
    protected $table = 'acquisition_checklists';

    protected $fillable = [
        'name',
        'steps',
        'is_template',
    ];

    public $with = [

    ];

    public function steps()
    {
        return $this->hasMany(Step::class, 'acquisition_checklist_id');
    }

    public function overdueSteps()
    {
        return $this->steps()->whereNull('completed_on')->whereDate('forecast_for', '<', Carbon::now());
    }

    public function site()
    {
        return $this->belongsTo(Site::class, 'acquisition_site_id');
    }

    public function bareSite()
    {
        return $this->site()->without((new Site())->with);
    }
}
