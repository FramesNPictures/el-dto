<?php

namespace Fnp\Dto\Common\Traits;

use Fnp\ElHelper\Iof;
use Fnp\ElHelper\Obj;
use Illuminate\Support\Arr;
use ReflectionProperty;

trait DtoFill
{
    /**
     * Populate items
     *
     * @param array|mixed $items Items to be populated into the model
     * @param array|null  $flags Additional options
     */
    public function fill($items, $flags = NULL): void
    {
        if (is_null($items)) {
            return;
        }

        if (!Arr::accessible($items) &&
            Iof::arrayable($items) &&
            !Iof::eloquent($items)) {
            $items = $items->toArray();
        }

        if ($items instanceof \stdClass) {
            $items = get_object_vars($items);
        }

        try {
            $reflection = new \ReflectionClass($this);
        } catch (\ReflectionException $e) {
            return;
        }

        // TODO: Use flags to decide which properties should be populated
        $vars = $reflection->getProperties(ReflectionProperty::IS_PRIVATE |
                                           ReflectionProperty::IS_PROTECTED +
                                           ReflectionProperty::IS_PUBLIC);

        foreach ($vars as $variable) {
            $variable->setAccessible(TRUE);
            $varName  = $variable->getName();
            $varValue = Arr::get($items, $varName);

            if (!is_null($varValue)) {
                $setter = Obj::methodExists($this, 'fill', $varName);

                if ($setter) {
                    $varValue = $this->$setter($varValue);
                    $variable->setValue($this, $varValue);
                } else {
                    $variable->setValue($this, $varValue);
                }
            }
        }
    }
}