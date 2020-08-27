<?php

namespace Fnp\Dto\Common\Traits;

use Fnp\Dto\Common\Flags\DtoFillFlags;
use Fnp\Dto\Common\Flags\DtoFlags;
use Fnp\Dto\Common\Flags\DtoToArrayFlags;
use Fnp\Dto\Common\Helper\Iof;
use Fnp\Dto\Common\Helper\Obj;
use Illuminate\Support\Arr;

trait DtoMapperFill
{
    /**
     * Populate items with mapping
     *
     * @param array|mixed $items
     * @param null        $flags
     */
    public function fill($items, $flags = NULL): void
    {
        if (!Arr::accessible($items) && Iof::arrayable($items)) {
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

        $vars  = $reflection->getProperties();
        $flags = new DtoFlags($flags);

        /** @var \ReflectionProperty $var */
        foreach ($vars as $variable) {

            $variable->setAccessible(TRUE);
            $targetVar = $variable->getValue($this);
            $var       = $variable->getName();

            if (is_null($targetVar)) {
                continue;
            }

            $value = Arr::get($items, $targetVar, NULL);

            if ($targetVar == '.') {
                $value = $items;
            }

            $setter = Obj::methodExists($this, 'fill', $var);

            if ($setter) {

                $value = $this->$setter($value);

                if (!is_null($value)) {
                    $variable->setValue($this, $value);
                } else {
                    if ($variable->getValue($this) == $targetVar) {
                        $variable->setValue($this, NULL);
                    }
                }

                continue;
            }

            $variable->setValue($this, $value);
        }
    }
}