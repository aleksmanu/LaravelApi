<?php

namespace App\Modules\Auth\Models;

use App\Modules\Account\Models\Account;
use App\Modules\Account\Models\AccountType;
use App\Modules\Acquisition\Models\Acquisition;
use App\Modules\Common\Classes\Abstracts\AuthenticatableBaseModel;
use App\Modules\Common\Models\Note;
use App\Modules\Property\Models\PropertyManager;
use App\Scopes\FilteredByClientScope;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Silber\Bouncer\Database\HasRolesAndAbilities;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends AuthenticatableBaseModel implements JWTSubject
{
    use Notifiable, SoftDeletes, HasRolesAndAbilities;

    public static function getTableName(): string
    {
        return 'users';
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'account_id', 'first_name', 'last_name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime:D, M d, Y',
        'updated_at' => 'datetime:D, M d, Y',
        'deleted_at' => 'datetime:D, M d, Y',
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

    /**
     * Custom fields that should always be returned with the Model
     *
     * @var array
     */
    protected $appends = ['role'];


    protected $with = ['roles', 'account.accountType'];

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /*
     * slack webhook URL for 'notifiable'
     */
    public function routeNotificationForSlack()
    {
        return 'https://hooks.slack.com/services/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';
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        $accountTypeIsClient = $this->account->accountType->slug === AccountType::CLIENT;
        return [
            'user' => [
                'f_name' => $this->first_name,
                'l_name' => $this->last_name,
                'email' => $this->email
            ],
            'restriction_account_id' => $accountTypeIsClient ? $this->account_id : 0,
            'is_system' => $this->isSystemUser()
        ];
    }

    /**
     * @return mixed
     */
    public function getRoleAttribute()
    {
        return $this->roles[0];
    }

    /**
     * @return bool
     */
    public function canByPassEdit(): bool
    {
        return $this->isAn(Role::AUTHORISER) || $this->isAn(Role::DEVELOPER);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function permissions()
    {
        return $this->getAbilities();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function account()
    {
        return $this->belongsTo(Account::class)->withoutGlobalScope(FilteredByClientScope::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function propertyManager()
    {
        return $this->hasOne(PropertyManager::class)->withoutGlobalScope(FilteredByClientScope::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function notes()
    {
        return $this->hasMany(Note::class);
    }

    public function acquisitions()
    {
        return $this->belongsToMany(Acquisition::class);
    }

    /**
     * @return string
     */
    public function getFullName(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    /**
     * @return bool
     */
    public function isSystemUser(): bool
    {
        return $this->account->accountType->slug === 'system';
    }

    /**
     * @return string
     */
    public function calculateInitials(): string
    {

        $names    = explode(" ", $this->getFullName());
        $initials = '';

        foreach ($names as $name) {
            $initials .= $name[0];
        }
        return $initials;
    }
}
