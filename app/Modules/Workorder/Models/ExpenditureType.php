<?php

namespace App\Modules\Workorder\Models;

use App\Modules\Common\Classes\Abstracts\BaseModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExpenditureType extends BaseModel
{
    use SoftDeletes;

    /**
     * @return string
     */
    public static function getTableName(): string
    {
        return 'expenditure_types';
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'code',
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
        'deleted_at',
        'created_at',
        'updated_at',
    ];

    public function quote()
    {
        return $this->hasMany(Quote::class);
    }
}
