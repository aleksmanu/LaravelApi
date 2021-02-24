<?php
namespace App\Modules\Acquisition\Models;

use App\Modules\Common\Classes\Abstracts\BaseModel;
use Illuminate\Support\Facades\Cache;

class PopArea extends BaseModel
{
    public static function getTableName(): string
    {
        return 'acquisition_pop_areas';
    }

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'name',
        'slug',
        'acquisition_id',
    ];

    protected $appends = [
        'overdue_step_count'
    ];

    public $with = [
    ];

    public function getOverdueStepCountAttribute()
    {
        return Cache::rememberForever("acquisition_popareas.{$this->id}.overdueStepsCount", function () {
            return $this->sites->sum('overdue_steps_count');
        });
    }

    public function acquisition()
    {
        return $this->belongsTo(Acquisition::class);
    }

    public function bareAcquisition()
    {
        return $this->acquisition()->without((new Acquisition())->with);
    }

    public function sites()
    {
        return $this->hasMany(Site::class)
            ->withCount(['overdueSteps']);
    }

    public function bareSites()
    {
        return $this->sites()->without((new Site())->with);
    }

    public function complete()
    {
        return $this->sites()
            ->where('status', 'complete');
            
    }

    public function active()
    {
        return $this->sites()
            ->where('status', 'active');
    }

    public function cancelled()
    {
        return $this->sites()
            ->where('status', 'cancelled');
    }

    public function none()
    {
        return $this->sites()->whereNull('status');
    }

    protected static function boot()
    {
        parent::boot();
        static::updated(function ($obj) {
            foreach ($obj->sites as $site) {
                foreach ($site->steps as $step) {
                    $step->invalidateCache();
                }
            }
        });
    }

    public function invalidateCache()
    {
        Cache::forget("acquisition_popareas.{$this->id}.overdueStepsCount");
    }
}
