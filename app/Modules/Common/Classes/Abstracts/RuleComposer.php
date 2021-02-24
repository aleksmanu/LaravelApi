<?php
/**
 * Created by PhpStorm.
 * User: aron
 * Date: 9/6/18
 * Time: 11:43 AM
 */

namespace App\Modules\Common\Classes\Abstracts;

abstract class RuleComposer
{
    abstract public static function getBaseRules();
    abstract public static function getCreateRules();
    abstract public static function getPatchRules();

    public static function appendRules($baseRules, $additions)
    {
        foreach ($additions as $key => $additionalRules) {
            $baseRules[$key] = array_merge($baseRules[$key], $additionalRules);
        }

        return $baseRules;
    }
}
