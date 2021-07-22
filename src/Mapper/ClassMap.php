<?php

namespace Fnp\Dto\Mapper;

use Fnp\ElHelper\Str;

class ClassMap
{
    private static $extensions;

    public static function classes()
    {
        return array_values(self::constants());
    }

    public static function handles()
    {
        return array_map(function ($v) {
            return static::generateHandle($v);
        }, array_keys(self::constants()));
    }

    public static function hasClass($class)
    {
        return in_array($class, self::classes());
    }

    public static function hasHandle($handle)
    {
        return in_array($handle, self::handles());
    }

    public static function getHandle($class, $default = NULL)
    {
        $match = self::constants();
        $match = array_flip($match);

        if (!isset($match[ $class ])) {
            return $default;
        }

        return static::generateHandle($match[ $class ]);
    }

    public static function getClass($handle, $default = NULL)
    {
        $match = self::constants();

        foreach ($match as $key => $value) {
            if (static::generateHandle($key) == $handle) {
                return $value;
            }
        }

        return $default;
    }

    protected static function generateHandle($string)
    {
        return Str::camel($string);
    }

    public static function extend($handle, $class)
    {
        self::$extensions[get_called_class()][$handle] = $class;
    }

    /**
     * Returns an array of constants
     *
     * @return array
     */
    public static function constants()
    {
        try {
            $reflection = new \ReflectionClass(get_called_class());
        } catch (\ReflectionException $e) {
            return [];
        }

        $constants = $reflection->getConstants();

        if (isset(self::$extensions[get_called_class()])) {
            $constants = array_merge($constants, self::$extensions[get_called_class()]);
        }

        return $constants;
    }
}