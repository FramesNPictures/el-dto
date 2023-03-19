<?php

namespace Fnp\Dto;

use Fnp\Dto\Contracts\AccessesDtoData;
use Fnp\Dto\Contracts\AccessesDtoModel;
use Fnp\Dto\Contracts\AccessesDtoPropertyName;
use Fnp\Dto\Contracts\ModifiesDtoValue;
use Fnp\Dto\Contracts\ReturnsValue;
use Fnp\Dto\Contracts\SetsDtoValue;
use Fnp\Dto\Exceptions\DtoClassNotExistsException;
use Fnp\ElHelper\Arr;
use Fnp\ElHelper\Flg;
use Fnp\ElHelper\Iof;
use Fnp\ElHelper\Obj;
use Illuminate\Support\Collection;

class Dto
{
    public const EXCLUDE_NULLS                 = 0b0000001;     // Exclude values with NULL
    public const DONT_SERIALIZE_OBJECTS        = 0b0000010;     // Do Not Serialize objects
    public const DONT_SERIALIZE_STRING_OBJECTS = 0b0000100;     // Serialize objects with __toString
    public const PREFER_STRING_PROVIDERS       = 0b0001000;     // Prefer String Providers over Object Serialization
    public const JSON_PRETTY                   = 0b0010000;     // Produce nicely formatted JSON

    public static function collection(
        $modelClassName,
        mixed $collection,
        int $flags = 0
    ): Collection {
        if ( ! $collection) {
            $collection = [];
        }

        if ( ! $modelClassName || ! class_exists($modelClassName, true)) {
            throw DtoClassNotExistsException::make($modelClassName);
        }

        if (Iof::arrayable($collection) && ! Iof::collection($collection)) {
            $collection = $collection->toArray($flags);
        }

        if ( ! Iof::collection($collection)) {
            $collection = new Collection($collection);
        }

        $collection = $collection->map(function ($item) use ($modelClassName, $flags) {
            $model = app($modelClassName);

            return self::fill($model, $item, $flags);
        });

        return $collection;
    }

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

        $vars = Obj::properties($model);

        foreach ($vars as $variable) {
            $variable->setAccessible(true);
            $varName  = $variable->getName();
            $varValue = null;

            // Map provided, but variable not on the list - ignore
            if (!is_null($map) && !isset($map[$varName])) {
                continue;
            }

            // No map provided -> try direct grab
            if (is_null($varValue) && is_null($map)) {
                $varValue = Arr::get($data, $varName);
            }

            // Map provided -> try grab by mapped name
            if (is_null($varValue) && ! is_null($map)) {
                $mappedVarName = Arr::get($map, $varName);

                if ( $mappedVarName instanceof \Closure) {
                    // If the map value is the closure -> assign the result
                    $varValue = $mappedVarName($model);
                } elseif ( ! is_null($mappedVarName)) {
                    // Otherwise -> assign the mapped value
                    $varValue = Arr::get($data, $mappedVarName);
                }
            }

            foreach ($variable->getAttributes() as $attrReflection) {
                $attribute = $attrReflection->newInstance();

                if ($attribute instanceof AccessesDtoModel) {
                    $attribute->setModel($model);
                }

                if ($attribute instanceof AccessesDtoData) {
                    $attribute->setData($data);
                }

                if ($attribute instanceof AccessesDtoPropertyName) {
                    $attribute->setPropertyName($varName);
                }

                if ($attribute instanceof ReturnsValue) {
                    $varValue = $attribute->getValue($data);
                }

                if ($attribute instanceof ModifiesDtoValue) {
                    $varValue = $attribute->modifyValue($varValue);
                }

                if ($attribute instanceof SetsDtoValue) {
                    $attribute->setValue($varValue);
                    $varValue = $variable->getValue($model);
                }
            }

            $variable->setValue($model, $varValue);
        }

        return $model;
    }

    public static function toArray(object $model, int $flags = 0): array
    {
        $array = [];

        if (Iof::collection($model)) {
            $model = $model->map(fn($m) => self::toArray($m, $flags));

            return $model->toArray();
        } elseif (Iof::arrayable($model)) {
            $array = $model->toArray();
        } elseif (Iof::serializable($model)) {
            $array = $model->__serialize();
        } else {
            $vars = Obj::properties($model, Obj::PROPERTIES_ACCESSIBLE);
            foreach ($vars as $varRef) {
                $varRef->setAccessible(true);
                $varName  = $varRef->getName();
                $varValue = $varRef->getValue($model);

                if ($getter = Obj::methodExists($model, 'get', $varName)) {
                    // Use getter if available
                    $array[$varName] = $model->$getter();
                } elseif (
                    Iof::stringable($varValue) &&
                    ! Flg::has($flags, self::DONT_SERIALIZE_STRING_OBJECTS) &&
                    Flg::has($flags, self::PREFER_STRING_PROVIDERS)
                ) {
                    // Use magic __toString to serialize string provider
                    $array[$varName] = $varValue->__toString();
                } elseif (
                    Iof::arrayable($varValue) &&
                    ! Flg::has($flags, self::DONT_SERIALIZE_OBJECTS)
                ) {
                    // Use explicit toArray method to serialize
                    $array[$varName] = $varValue->toArray();
                } elseif (
                    Iof::serializable($varValue) &&
                    ! Flg::has($flags, self::DONT_SERIALIZE_OBJECTS)
                ) {
                    // Use magik __serialize method to serialize object
                    $array[$varName] = $varValue->__serialize();
                } elseif (
                    is_object($varValue) &&
                    ! Iof::stringable($varValue) &&
                    ! Flg::has($flags, self::DONT_SERIALIZE_STRING_OBJECTS) &&
                    ! Flg::has($flags, self::PREFER_STRING_PROVIDERS) &&
                    ! Flg::has($flags, self::DONT_SERIALIZE_OBJECTS)
                ) {
                    // Apply toArray to the object
                    $array[$varName] = self::toArray($varValue, $flags);
                } elseif (
                    Iof::stringable($varValue) &&
                    ! Flg::has($flags, self::DONT_SERIALIZE_STRING_OBJECTS) &&
                    ! Flg::has($flags, self::PREFER_STRING_PROVIDERS)
                ) {
                    $array[$varName] = $varValue->__toString();
                } else {
                    $array[$varName] = $varValue;
                }
            }
        }

        return $array;
    }

    public static function toJson(object $model, int $flags = 0): string
    {
        $jsonFlags = 0;
        if (Flg::has($flags, self::JSON_PRETTY)) {
            $jsonFlags = Flg::set($jsonFlags, JSON_PRETTY_PRINT);
        }

        return json_encode(self::toArray($model, $flags), $jsonFlags);
    }
}
