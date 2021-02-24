<?php

namespace App\Modules\Common\Classes\Abstracts;

use App\Modules\Common\Traits\RandomRowScopeTrait;
use Illuminate\Database\Eloquent\Model;

abstract class BaseModel extends Model
{
    use RandomRowScopeTrait;

    abstract public static function getTableName(): string;

    public function __construct(array $attributes = [])
    {
        $this->table = static::getTableName();
        parent::__construct($attributes);
    }
}
