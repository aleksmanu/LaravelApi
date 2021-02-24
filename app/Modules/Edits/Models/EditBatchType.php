<?php

namespace App\Modules\Edits\Models;

use Illuminate\Database\Eloquent\Model;

class EditBatchType extends Model
{

    const FLAG   = 'flag';
    const EDIT   = 'edit';
    const CREATE = 'create';

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
    public function editBatches()
    {
        return $this->hasMany(EditBatch::class);
    }
}
