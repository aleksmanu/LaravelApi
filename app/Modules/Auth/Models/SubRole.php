<?php

namespace App\Modules\Auth\Models;

use Illuminate\Database\Eloquent\Model;

class SubRole extends Model
{
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

    public function role()
    {
        return $this->belongsToMany(Role::class);
    }
}
