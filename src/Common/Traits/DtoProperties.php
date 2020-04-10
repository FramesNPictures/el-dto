<?php

namespace Fnp\Dto\Common\Traits;

trait DtoProperties
{
    /**
     * Returns a list of properties
     *
     * @return \ReflectionProperty[]|void
     */
    public static function __properties($flags)
    {
        try {
            $reflection = new \ReflectionClass(get_called_class());
        } catch (\ReflectionException $e) {
            return;
        }

        return $reflection->getProperties();
    }
}