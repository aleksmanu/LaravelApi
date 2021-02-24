<?php

namespace App\Modules\Common\Traits;

use \Webpatser\Uuid\Uuid;

trait UuidModelTrait
{
    protected static function bootUuidModelTrait()
    {
        static::creating(function ($model) {
            $model->{$model->getKeyName()} = Uuid::generate()->string;
        });
    }
}
