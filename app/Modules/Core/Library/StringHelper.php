<?php

namespace App\Modules\Core\Library;

class StringHelper
{

    /**
     * @param $string
     * @return string
     */
    public static function slugify($string): string
    {
        $string = str_replace(" ", "_", strtolower($string));
        return $string;
    }

    /**
     * @param $str
     * @return string
     */
    public static function snakeCaseToCamelCase($str)
    {
        return lcfirst(str_replace('_', '', ucwords($str, '_')));
    }
}
