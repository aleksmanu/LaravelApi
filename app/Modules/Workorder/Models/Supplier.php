<?php

namespace App\Modules\Workorder\Models;

use App\Modules\Common\Classes\Abstracts\BaseModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends BaseModel
{
    use SoftDeletes;

    /**
     * @return string
     */
    public static function getTableName(): string
    {
        return 'suppliers';
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'phone',
        'email'
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

    public function quote()
    {
        return $this->hasMany(Quote::class);
    }
}
