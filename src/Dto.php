<?php

namespace Fnp\Dto;

use Fnp\Dto\Contracts\AccessesDtoModel;
use Fnp\Dto\Contracts\ModifiesDtoValue;
use Fnp\Dto\Contracts\ObtainsValue;
use Fnp\ElHelper\Arr;
use Fnp\ElHelper\Iof;

class Dto
{
    /**
     * Fill the properties of the object with the data.
     * Optionally use mapping information to map the variables.
     *
     * @param  object      $model
     * @param  mixed       $data
     * @param  mixed|null  $map
     *
     * @return object
     */
    public static function fill(
        object $model,
        mixed $data,
        mixed $map = null,
    ): object {

        if (is_null($data)) {
            return $model;
        }

        if ( ! Arr::accessible($data) &&
             Iof::arrayable($data) &&
             ! Iof::eloquentModel($data)) {
            $data = $data->toArray();
        }

        if ($data instanceof \stdClass) {
            $data = get_object_vars($data);
        }

        $vars = self::properties(
            $model,
            self::INCLUDE_PUBLIC + self::INCLUDE_PROTECTED + self::INCLUDE_PRIVATE,
            $flags
        );

        foreach ($vars as $variable) {
            $variable->setAccessible(true);
            $varName  = $variable->getName();
            $varValue = null;

            foreach ($variable->getAttributes() as $attrReflection) {
                $attribute = $attrReflection->newInstance();

                if ($attribute instanceof AccessesDtoModel) {
                    $attribute->setModel($model);
                }

                if ($attribute instanceof ObtainsValue) {
                    $varValue = $attribute->getValue($data);
                }

                if ($attribute instanceof ModifiesDtoValue) {
                    $varValue = $attribute->modifyValue($varValue);
                }
            }

            // No map provided -> try direct grab
            if (is_null($varValue) && is_null($map)) {
                $varValue = Arr::get($data, $varName);
            }

            // Map provided -> try grab by mapped name
            if (is_null($varValue) && ! is_null($map)) {
                $mappedVarName = Arr::get($map, $varName);
                if ( ! is_null($mappedVarName)) {
                    $varValue = Arr::get($data, $mappedVarName);
                }
            }

            $variable->setValue($model, $varValue);
        }

        return $model;
    }
}
