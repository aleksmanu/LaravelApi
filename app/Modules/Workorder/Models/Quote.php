<?php

namespace App\Modules\Workorder\Models;

use App\Modules\Auth\Models\User;
use App\Modules\Property\Models\Property;
use App\Modules\Property\Models\Unit;

use App\Modules\Common\Classes\Abstracts\BaseModel;
use App\Scopes\FilteredByClientScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Quote extends BaseModel
{
    //use SoftDeletes;

    /**
     * @return string
     */
    public static function getTableName(): string
    {
        return 'quotes';
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'property_id',
        'unit_id',
        'supplier_id',
        'locked_by_id',
        'expenditure_type_id',
        'value',
        'work_description',
        'critical_information',
        'contact_details',
        'booked_at',
        'due_at',
        'locked_note',
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
        'due_at',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'value' => 'float',
    ];

    protected $with = [
        'lockedBy',
        'workOrder',
        'property',
        'unit',
        'supplier',
        'expenditureType'
    ];

    protected $appends = [
        'isAccepted'
    ];

    public function getIsAcceptedAttribute()
    {
        return $this->workOrder()->exists();
    }

    public function property()
    {
        return $this->belongsTo(Property::class)->withoutGlobalScope(FilteredByClientScope::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class)->withoutGlobalScope(FilteredByClientScope::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function expenditureType()
    {
        return $this->belongsTo(ExpenditureType::class);
    }

    public function workOrder()
    {
        return $this->hasOne(WorkOrder::class);
    }

    public function lockedBy()
    {
        return $this->belongsTo(User::class);
    }
}
