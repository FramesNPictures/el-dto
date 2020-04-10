<?php

namespace Fnp\Dto\Common\Traits;

trait DtoConstants
{
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

        return $constants;
    }
}