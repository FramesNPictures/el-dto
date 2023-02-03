<?php

namespace Fnp\Dto;

use Fnp\Dto\Contracts\AccessesDtoModel;
use Fnp\Dto\Contracts\ModifiesDtoValue;
use Fnp\Dto\Contracts\ReturnsValue;
use Fnp\Dto\Exceptions\DtoClassNotExistsException;
use Fnp\ElHelper\Arr;
use Fnp\ElHelper\Exceptions\CouldNotAccessProperties;
use Fnp\ElHelper\Flg;
use Fnp\ElHelper\Iof;
use Fnp\ElHelper\Obj;
use Illuminate\Support\Collection;
use ReflectionProperty;

class DtoLegacy
{
    public const INCLUDE_PUBLIC          = 0b0000000000001;                // Include public properties
    public const EXCLUDE_PUBLIC          = 0b0000000000010;                // Exclude public properties
    public const INCLUDE_PROTECTED       = 0b0000000000100;                // Include protected properties
    public const EXCLUDE_PROTECTED       = 000000000001000;                // Exclude protected properties
    public const INCLUDE_PRIVATE         = 0b0000000010000;                // Include private properties
    public const EXCLUDE_PRIVATE         = 0b0000000100000;                // Exclude private properties
    public const EXCLUDE_NULLS           = 0b0000001000000;                // Exclude values with NULL
    public const DONT_SERIALIZE_OBJECTS  = 0b0000010000000;                // Do Not Serialize objects
    public const DONT_SERIALIZE_STRINGS  = 0b0000100000000;                // Serialize objects with __toString
    public const PREFER_STRING_PROVIDERS = 0b0001000000000;                // Prefer String Providers over Object Serialization
    public const JSON_PRETTY             = 0b0010000000000;                // Produce nicely formatted JSON

    public static function collection(
        $modelClass,
        mixed $collection,
        int $flags = 0
    ): Collection {
        if ( ! $collection) {
            $collection = [];
        }

        if ( ! $modelClass || ! class_exists($modelClass, true)) {
            throw DtoClassNotExistsException::make($modelClass);
        }

        if (Iof::arrayable($collection) && ! Iof::collection($collection)) {
            $collection = $collection->toArray($flags);
        }

        if ( ! Iof::collection($collection)) {
            $collection = new Collection($collection);
        }

        $collection = $collection->map(function ($item) use ($modelClass, $flags) {
            $model = app($modelClass);

            return self::fill($model, $item, $flags);
        });

        return $collection;
    }

    /**
     * @param  object  $model
     * @param  mixed   $data
     * @param  int     $flags
     *
     * @return object
     * @throws CouldNotAccessProperties
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

                if ($attribute instanceof ReturnsValue) {
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

    /**
     * @param  object  $from
     * @param  object  $to
     * @param  object  $definition
     * @param  int     $flags
     *
     * @return object
     * @throws CouldNotAccessProperties
     */
    public static function map(
        object $from,
        object $to,
        object $definition,
        int $flags = 0
    ): object {
        $attributes = [];

        $fromAttributes = self::toArray($from, $flags);

        foreach (self::toArray($definition) as $defKey => $defValue) {
            $defValue = (array) $defValue; // Make sure array
            foreach ($defValue as $mapKey) {
                // Attempt to grab the value
                $mapValue = Arr::get($fromAttributes, $mapKey, '!**NOTFOUND**__');

                if ($mapValue === '!**NOTFOUND**__') {
                    continue; // Ignore if not exists in source
                }

                if (Flg::has($flags, self::EXCLUDE_NULLS) and is_null($mapValue)) {
                    continue; // Ignore if null and exclude nulls
                }

                if ($mapMethod = Obj::methodExists($definition, 'map', $defKey)) {
                    $mapValue = $definition->$mapMethod($mapValue); // Modify the value
                }

                $attributes[$defKey] = $mapValue;

                break; // Do not attempt the rest of maps if found
            }
        }

        self::fill($to, $attributes, $flags);

        return $to;
    }

    /**
     * @param  object  $model
     * @param  int     $flags
     *
     * @return array
     * @throws CouldNotAccessProperties
     */
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

            $vars = self::properties(
                $model,
                self::INCLUDE_PUBLIC + self::INCLUDE_PROTECTED,
                $flags
            );

            /** @var ReflectionProperty $varRef */
            foreach ($vars as $varRef) {

                $varRef->setAccessible(true);
                $varName  = $varRef->getName();
                $varValue = $varRef->getValue($model);

                if ($getter = Obj::methodExists($model, 'get', $varName)) {
                    $array[$varName] = $model->$getter();
                } elseif (
                    Iof::stringable($varValue) &&
                    ! Flg::has($flags, self::DONT_SERIALIZE_STRINGS) &&
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
                    ! Flg::has($flags, self::DONT_SERIALIZE_STRINGS) &&
                    ! Flg::has($flags, self::PREFER_STRING_PROVIDERS) &&
                    ! Flg::has($flags, self::DONT_SERIALIZE_OBJECTS)
                ) {
                    // Apply toArray to the object
                    $array[$varName] = self::toArray($varValue, $flags);
                } elseif (
                    Iof::stringable($varValue) &&
                    ! Flg::has($flags, self::DONT_SERIALIZE_STRINGS) &&
                    ! Flg::has($flags, self::PREFER_STRING_PROVIDERS)
                ) {
                    $array[$varName] = $varValue->__toString();
                } else {
                    $array[$varName] = $varValue;
                }
            }
        }

        if (Flg::has($flags, self::EXCLUDE_NULLS)) {
            $array = array_filter($array, function ($value) {
                return ! is_null($value);
            });
        }

        return $array;
    }

    /**
     * @param  object  $model
     * @param  int     $flags
     *
     * @return string
     * @throws CouldNotAccessProperties
     */
    public static function toJson(object $model, int $flags = 0): string
    {
        $array     = self::toArray($model, $flags);
        $jsonFlags = 0;

        if (Flg::has($flags, self::JSON_PRETTY)) {
            $jsonFlags = JSON_PRETTY_PRINT;
        }

        return json_encode($array, $jsonFlags);
    }

    /**
     * @param  object  $model
     * @param  int     $flags
     *
     * @return string
     */
    public static function serialize(object $model, int $flags = 0): string
    {
        return ''; // TODO: To be implemented
    }

    /**
     * @param  string  $model
     *
     * @return object
     */
    public static function deserialize(string $model): object
    {
        return new static; // TODO: To be implemented
    }

    /**
     * @param  int  $default
     * @param  int  $flags
     *
     * @return int
     */
    protected static function reflectionFilter(int $default, int $flags): int
    {
        $filter = 0;

        $logic = [
            0 => [
                self::EXCLUDE_PUBLIC    => ReflectionProperty::IS_PUBLIC,
                self::EXCLUDE_PROTECTED => ReflectionProperty::IS_PROTECTED,
                self::EXCLUDE_PRIVATE   => ReflectionProperty::IS_PRIVATE,
            ],
            1 => [
                self::INCLUDE_PUBLIC    => ReflectionProperty::IS_PUBLIC,
                self::INCLUDE_PROTECTED => ReflectionProperty::IS_PROTECTED,
                self::INCLUDE_PRIVATE   => ReflectionProperty::IS_PRIVATE,
            ],
        ];

        foreach ([$default, $flags] as $flagSet) {
            foreach ($logic as $operation => $map) {
                foreach ($map as $index => $propertyFilterFlag) {
                    if (Flg::has($flagSet, $index)) {
                        if ($operation) {
                            $filter = Flg::set($filter, $propertyFilterFlag);
                        } else {
                            $filter = Flg::clear($filter, $propertyFilterFlag);
                        }
                    }
                }
            }
        }

        return $filter;
    }
}
