<?php

namespace App\Modules\Edits\Models;

use App\Modules\Auth\Models\User;
use App\Modules\Client\Models\ClientAccount;
use App\Modules\Client\Models\Portfolio;
use App\Modules\Lease\Models\Lease;
use App\Modules\Lease\Models\Tenant;
use App\Modules\Property\Models\Property;
use App\Modules\Property\Models\Unit;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class EditBatch extends Model
{

    /**
     * @var array
     */
    protected $fillable = [
        'edit_batch_type_id',
        'reviewed_by_user_id',
        'created_by_user_id',
        'client_account_id',
        'entity_type',
        'entity_id',
        'name',
        'reviewed_at',
        'status_changed_at'
    ];

    /**
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
        'reviewed_at',
        'status_changed_at'
    ];

    /**
     * @var array
     */
    protected $appends = [
        'days_in_status',
    ];

    /**
     * @return mixed
     */
    public function getDaysInStatusAttribute()
    {
        return $this->status_changed_at->diffInDays(Carbon::now());
    }

    /**
     * @return ClientAccount|null
     */
    public function getClientAccountAttribute()
    {
        if (!isset($this->entity)) {
            return null;
        }

        if ($this->entity_type === ClientAccount::class) {
            return $this->entity;
        } elseif ($this->entity_type === Portfolio::class) {
            return $this->entity->clientAccount;
        } elseif ($this->entity_type === Property::class) {
            return $this->entity->portfolio->clientAccount;
        } elseif ($this->entity_type === Unit::class) {
            return $this->entity->property->portfolio->clientAccount;
        } elseif ($this->entity_type === Lease::class) {
            return $this->entity->unit->property->portfolio->clientAccount;
        } elseif ($this->entity_type === Tenant::class) {
            return $this->entity->lease->unit->property->portfolio->clientAccount;
        } else {
            return null;
        }
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function editBatchType()
    {
        return $this->belongsTo(EditBatchType::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function createdByUser()
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function updatedByUser()
    {
        return $this->belongsTo(User::class, 'updated_by_user_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function reviewedByUser()
    {
        return $this->belongsTo(User::class, 'reviewed_by_user_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function edits()
    {
        return $this->hasMany(Edit::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function entity()
    {
        return $this->belongsTo($this->entity_type, 'entity_id');
    }
}
