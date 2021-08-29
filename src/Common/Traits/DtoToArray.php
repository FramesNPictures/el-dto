<?php

namespace Fnp\Dto\Common\Traits;

use Fnp\Dto\Common\Flags\Dto;
use Fnp\ElHelper\Iof;
use Fnp\ElHelper\Obj;
use ReflectionProperty;

trait DtoToArray
{
    /**
     * @param array|int Additional flags from Dto flags
     *
     * @return array
     * @throws \ReflectionException
     */
    public function toArray($flags = NULL)
    {
        $flags      = new Dto($flags);
        $reflection = new \ReflectionClass($this);
        $vars       = $reflection->getProperties(
            $flags->toArrayReflectionOptions()
        );

        $array = [];

        /** @var ReflectionProperty $varRef */
        foreach ($vars as $varRef) {

            $varRef->setAccessible(TRUE);
            $varName  = $varRef->getName();
            $varValue = $varRef->getValue($this);

            if ($getter = Obj::methodExists($this, 'get', $varName)) {
                $array[ $varName ] = $this->$getter();
            } elseif (
                Iof::stringable($varValue) &&
                $flags->serializeStringProviders() &&
                $flags->preferStringProviders()
            ) {
                $array[ $varName ] = $varValue->__toString();
            } elseif (
                Iof::arrayable($varValue) &&
                $flags->serializeObjects()
            ) {
                $array[ $varName ] = $varValue->toArray();
            } elseif (
                Iof::stringable($varValue) &&
                $flags->serializeStringProviders() &&
                !$flags->preferStringProviders()
            ) {
                $array[ $varName ] = $varValue->__toString();
            } else {
                $array[ $varName ] = $varValue;
            }
        }

        if ($flags->excludeNulls())
            $array = array_filter($array, function ($value) {
                return !is_null($value);
            });

        return $array;
    }
}