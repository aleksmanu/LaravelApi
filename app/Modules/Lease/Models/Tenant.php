<?php

namespace App\Modules\Lease\Models;

use App\Modules\Auth\Models\User;
use App\Modules\Common\Classes\Abstracts\BaseModel;
use App\Modules\Edits\Models\ReviewStatus;
use App\Modules\Edits\Traits\Editable;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Modules\Attachments\Traits\Attachable;

class Tenant extends BaseModel
{
    use SoftDeletes, Editable,  Attachable;

    /**
     * @return string
     */
    public static function getTableName(): string
    {
        return 'tenants';
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'tenant_status_id',
        'lease_id',
        'review_status_id',
        'locked_by_user_id',
        'name',
        'yardi_tenant_ref',
        'yardi_tenant_alt_ref',
        'locked_at'
    ];

    /**
     * Eager load attachments
     * @var array
     */
    protected $with = [
        //'attachments'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [

    ];

    /**
     * @var array
     */
    protected $editable = [
        'name',
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
        'locked_at'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function tenantStatus()
    {
        return $this->belongsTo(TenantStatus::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function lease()
    {
        return $this->belongsTo(Lease::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function reviewStatus()
    {
        return $this->belongsTo(ReviewStatus::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function lockedByUser()
    {
        return $this->belongsTo(User::class, 'locked_by_user_id');
    }
}
