<?php

namespace App\Modules\TestFramework\Helpers;

use Carbon\Carbon;
use Tests\TestCase;

class InstanceHelper extends TestCase
{

    /**
     * A wrapper to test model instances
     * @param $model
     * @param $instance
     * @param $params
     */
    public static function assertInstance($model, $instance, $params)
    {
        self::assertInstanceOf($model, $instance);
        foreach ($params as $key => $val) {
            self::assertTrue(isset($instance->$key) || is_null($instance->$key));
            if (isset($val)) {
                if ($instance->$key instanceof Carbon) {
                    self::assertEquals($val, $instance->$key);
                } else {
                    self::assertSame($val, $instance->$key);
                }
            }
        }
    }

    /**
     * @param $instance
     * @param $params
     */
    public static function assertArray($instance, $params)
    {
        self::assertInternalType('array', $instance);

        foreach ($params as $key => $val) {
            self::assertTrue(isset($instance[$key]) || is_null($instance[$key]));

            if (isset($val)) {
                if ($instance[$key] instanceof Carbon) {
                    self::assertEquals($val, $instance[$key]);
                } else {
                    self::assertSame($val, $instance[$key]);
                }
            }
        }
    }
}
