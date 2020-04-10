<?php

namespace Fnp\Dto\Common\Helper;

/**
 * Class Iof (Instance Of)
 *
 * @package Fnp\Dto\Common\Helper
 */
class Iof
{
    public static function arrayable($object)
    {
        if (!is_object($object))
            return FALSE;

        return method_exists($object, 'toArray');
    }

    public static function stringable($object)
    {
        if (!is_object($object))
            return FALSE;

        return method_exists($object, '__toString');
    }

    public static function jsonable($object)
    {
        if (!is_object($object))
            return FALSE;

        return method_exists($object, 'toJson');
    }

    public static function collection($object)
    {
        if (!is_object($object))
            return FALSE;

        return method_exists($object, 'contains') &&
               method_exists($object, 'push') &&
               method_exists($object, 'tap');
    }

    public static function eloquent($object)
    {
        if (!is_object($object))
            return FALSE;

        return method_exists($object, 'getRouteKeyName');
    }
}