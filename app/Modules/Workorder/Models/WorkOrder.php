<?php

namespace App\Modules\Workorder\Models;

use App\Modules\Common\Classes\Abstracts\BaseModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorkOrder extends BaseModel
{
    //use SoftDeletes;

    /**
     * @return string
     */
    public static function getTableName() : string
    {
        return 'work_orders';
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'quote_id',
        'value',
        'completed_at',
        'paid_at',
        'locked_note',
        'locked_by_id',
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
        'completed_at',
        'paid_at',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'value' => 'float',
    ];

    protected $with = [
      'quote'
    ];

    public function quote()
    {
        return $this->belongsTo(Quote::class)->without('workOrder');
    }
}
