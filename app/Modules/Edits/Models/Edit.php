<?php

namespace App\Modules\Edits\Models;

use App\Modules\Common\Models\Note;
use App\Modules\Common\Traits\HasNotes;
use Illuminate\Database\Eloquent\Model;

class Edit extends Model
{
    use HasNotes;

    /**
     * @var array
     */
    protected $fillable = [
        'edit_batch_id',
        'edit_status_id',
        'field',
        'previous_value',
        'value',
        'foreign_entity'
    ];

    /**
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
    ];

    /**
     * @var array
     */
    protected $appends = [
        'field_type'
    ];

    /**
     * @return string
     */
    public function getFieldTypeAttribute()
    {
        if ($this->editBatch) {
            return \Schema::getColumnType($this->editBatch->entity->getTable(), $this->field);
        }
        return null;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function editBatch()
    {
        return $this->belongsTo(EditBatch::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function editStatus()
    {
        return $this->belongsTo(EditStatus::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function previousForeignEntity()
    {
        return $this->belongsTo($this->foreign_entity, 'previous_value');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function proposedForeignEntity()
    {
        return $this->belongsTo($this->foreign_entity, 'value');
    }
}
