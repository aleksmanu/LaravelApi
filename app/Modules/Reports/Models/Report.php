<?php
namespace App\Modules\Reports\Models;

use App\Modules\Common\Classes\Abstracts\BaseModel;

class Report extends BaseModel
{
    public $timestamps = false;

    /**
     * @return string
     */
    public static function getTableName(): string
    {
        return 'reports';
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'slug',
        'source'
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
    protected $dates = [];

    public $with = [
        'reportColumns',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function reportColumns()
    {
        return $this->hasMany(ReportColumn::class)->orderBy('arrangement', 'ASC');
    }
}
