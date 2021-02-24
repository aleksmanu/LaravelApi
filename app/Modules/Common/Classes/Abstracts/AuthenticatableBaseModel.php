<?php
/**
 * Created by PhpStorm.
 * User: aron
 * Date: 9/25/18
 * Time: 10:55 AM
 */

namespace App\Modules\Common\Classes\Abstracts;

use App\Modules\Common\Traits\RandomRowScopeTrait;
use Illuminate\Foundation\Auth\User as Authenticatable;

abstract class AuthenticatableBaseModel extends Authenticatable
{
    use RandomRowScopeTrait;

    abstract public static function getTableName(): string;

    public function __construct(array $attributes = [])
    {
        $this->table = static::getTableName();
        parent::__construct($attributes);
    }
}
