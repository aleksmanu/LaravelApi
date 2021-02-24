<?php

namespace App\Modules\Property\Models;

use App\Modules\Common\Classes\Abstracts\BaseModel;
use App\Scopes\FilteredByClientScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LocationType extends BaseModel
{

    use SoftDeletes;

    /**
     * @return string
     */
    public static function getTableName(): string
    {
        return 'location_types';
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'slug',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [

    ];

    /**
     * The attributes that should be cast to Date objects
     *
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function properties()
    {
        return $this->hasMany(Property::class)->withoutGlobalScope(FilteredByClientScope::class);
    }
}
