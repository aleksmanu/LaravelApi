<?php

namespace App\Modules\Lease\Models;

use App\Modules\Common\Classes\Abstracts\BaseModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaidStatus extends BaseModel
{

    use SoftDeletes;

    public static function getTableName(): string
    {
        return 'paid_statuses';
    }

    /**
     * @var array
     */
    protected $fillable = [
        'name'
    ];

    /**
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
