<?php

namespace App\Modules\Attachments\Models;

use App\Modules\Common\Classes\Abstracts\BaseModel;

class DocumentLevel extends BaseModel
{
    public $timestamps = false;

    public static function getTableName(): string
    {
        return 'document_levels';
    }

    public function documentTypes()
    {
        return $this->belongsToMany(DocumentType::class);
    }
}
