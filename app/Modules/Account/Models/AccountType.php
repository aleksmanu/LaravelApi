<?php
namespace App\Modules\Account\Models;

use App\Modules\Common\Classes\Abstracts\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccountType extends BaseModel
{
    const SYSTEM   = 'system';
    const CLIENT   = 'client';
    const EXTERNAL = 'external';

    use SoftDeletes;

    public static function getTableName(): string
    {
        return 'account_types';
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'slug',
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

    public function accounts()
    {
        return $this->hasMany(Account::class);
    }
}
