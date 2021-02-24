<?php

namespace App\Modules\Attachments\Models;

use App\Modules\Common\Classes\Abstracts\BaseModel;

class DocumentCategory extends BaseModel
{
    public $timestamps = false;

    public static function getTableName(): string
    {
        return 'document_categories';
    }

    /**
     * Mutable fields
     *
     * @var array
     */
    protected $editable = [
        'name'
    ];

    /**
     * Bulk-assignable fields
     *
     * @var array
     */
    protected $fillable = [
        'name'
    ];

    /***
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function uploads()
    {
        return $this->hasMany(Document::class);
    }

    public function documentTypes()
    {
        return $this->hasMany(DocumentType::class);
    }
}
