<?php

namespace App\Modules\Core\Library;

class DatabaseHelper
{

    /**
     * @param $table
     * @param $field
     * @return mixed
     */
    public static function getFieldType($table, $field)
    {
        return \DB::connection()->getDoctrineColumn($table, $field)->getType()->getName();
    }
}
