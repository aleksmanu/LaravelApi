<?php
namespace App\Modules\Lease\Models;

use App\Modules\Common\Classes\Abstracts\BaseModel;

class LeaseReview extends BaseModel
{
    public static function getTableName(): string
    {
        return 'lease_reviews';
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'entity_id',
        'entity_type',
        'date',
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
        'date',
    ];

    protected $with = [
    ];

    public function parent()
    {
        return $this->morphTo();
    }
}
