<?php

namespace App\Modules\Client\Models;

use App\Modules\Common\Classes\Abstracts\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClientAccountStatus extends BaseModel
{

    use SoftDeletes;

    /**
     * @return string
     */
    public static function getTableName(): string
    {
        return 'client_account_statuses';
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'slug'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [

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
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function clientAccounts()
    {
        return $this->hasMany(ClientAccount::class);
    }
}
