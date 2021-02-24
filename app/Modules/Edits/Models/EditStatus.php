<?php

namespace App\Modules\Edits\Models;

use Illuminate\Database\Eloquent\Model;

class EditStatus extends Model
{

    const PENDING  = 'pending';
    const APPROVED = 'approved';
    const REJECTED = 'rejected';

    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'slug'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function edits()
    {
        return $this->hasMany(Edit::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function editBatches()
    {
        return $this->hasManyThrough(EditBatch::class, Edit::class);
    }

    /**
     * @return bool
     */
    public function isPending(): bool
    {
        return $this->slug === self::PENDING;
    }

    /**
     * @return bool
     */
    public function isApproved(): bool
    {
        return $this->slug === self:: APPROVED;
    }

    /**
     * @return bool
     */
    public function isRejected(): bool
    {
        return $this->slug === self::REJECTED;
    }
}
