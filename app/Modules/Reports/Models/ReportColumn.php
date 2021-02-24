<?php
namespace App\Modules\Reports\Models;

use App\Modules\Common\Classes\Abstracts\BaseModel;

class ReportColumn extends BaseModel
{
    public $timestamps = false;

    /**
     * @return string
     */
    public static function getTableName(): string
    {
        return 'report_columns';
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'report_id',
        'preview',
        'name',
        'attribute',
        'arrangement',
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

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function report()
    {
        return $this->belongsTo(Report::class);
    }
}
