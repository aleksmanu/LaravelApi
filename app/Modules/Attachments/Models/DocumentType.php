<?php

namespace App\Modules\Attachments\Models;

use App\Modules\Common\Classes\Abstracts\BaseModel;

class DocumentType extends BaseModel
{
    public $timestamps = false;

    public static function getTableName(): string
    {
        return 'document_types';
    }

    protected $fillable = [
        'name',
        'document_category_id'
    ];

    protected $with = [
        'documentCategory',
        'documentLevels'
    ];

    public function documentCategory()
    {
        return $this->belongsTo(DocumentCategory::class);
    }

    public function documentLevels()
    {
        return $this->belongsToMany(DocumentLevel::class);
    }
}
