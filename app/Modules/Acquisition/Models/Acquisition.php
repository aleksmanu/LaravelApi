<?php

namespace App\Modules\Acquisition\Models;

use App\Modules\Auth\Models\User;
use App\Modules\Common\Classes\Abstracts\BaseModel;
use Illuminate\Database\Eloquent\Model;
use \Illuminate\Database\Eloquent\Relations;
use Illuminate\Database\Query\Builder;

class Acquisition extends BaseModel
{
    public static function getTableName(): string
    {
        return 'acquisition_acquisitions';
    }

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
        'commence_at',
    ];

    protected $fillable = [
        'name',
        'account_id',
        'commence_at'
    ];

    public $with = [
        'popAreas',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function popAreas()
    {
        return $this->hasMany(PopArea::class);
    }

    public function sites()
    {
        return $this->hasManyThrough(Site::class, PopArea::class)
            ->where('acquisition_sites.status', '!=', 'cancelled');
    }

    protected static function boot()
    {
        parent::boot();
        static::updated(function ($obj) {
            if (in_array('name', array_keys($obj->getDirty()))) {
                foreach ($obj->sites as $site) {
                    foreach ($site->steps as $step) {
                        $step->invalidateCache();
                    }
                }
            }
        });
    }
}
