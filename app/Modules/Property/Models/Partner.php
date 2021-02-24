<?php
namespace App\Modules\Property\Models;

use App\Modules\Common\Classes\Abstracts\BaseModel;
use phpDocumentor\Reflection\DocBlock\Tags\Property;

class Partner extends BaseModel
{
    /**
     * @return string
     */
    public static function getTableName(): string
    {
        return 'partners';
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * The attributes that should be cast to Date objects
     *
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function properties()
    {
        return $this->hasMany(Property::class);
    }
}
