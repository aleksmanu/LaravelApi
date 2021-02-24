<?php

namespace App\Modules\Auth\Models;

use App\Modules\Auth\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
    const DEVELOPER  = 'developer';
    const ADMIN      = 'admin';
    const EDITOR     = 'editor';
    const AUTHORISER = 'authoriser';

    const CLIENT     = 'client';

    const E_DESIGNER = 'e_designer';
    const E_PLANNER = 'e_planner';
    const E_SOLICITOR = 'e_solicitor';
    const E_SURVEYOR = 'e_surveyor';

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
    protected $hidden = [

    ];

    protected $with = [
        //'subRole'
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

    public function permissions()
    {
        return $this->belongsToMany(Permission::class);
    }

    public function roleTemplate()
    {
        return $this->belongsTo(RoleTemplate::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subRole()
    {
        return $this->hasMany(SubRole::class);
    }

    /**
     * Extend the eloquent functionality to add some self explanatory lifecycle hooks
     */
    protected static function boot()
    {
        parent::boot();

        static::created(function ($role) {
            $role->roleTemplate->permissions->each(function ($perm) use ($role) {
                $role->permissions()->attach($perm);
            });
        });

        static::deleting(function ($role) {
        });
    }
}
