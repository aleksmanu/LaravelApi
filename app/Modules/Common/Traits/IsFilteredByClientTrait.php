<?php
namespace App\Modules\Common\Traits;

trait IsFilteredByClientTrait
{
    abstract public static function getJoinPathToAccount(): array;
}
