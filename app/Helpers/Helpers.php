<?php
namespace App\Helpers;

class Helpers
{
    public static function convertStringToBool($string)
    {
        $arrayOfTruthyStrings = [
            'yes',
            'y',
            'true',
            "'true'",
            1,
        ];
        return in_array(strtolower($string), $arrayOfTruthyStrings);
    }
}
