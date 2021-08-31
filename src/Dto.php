<?php

namespace Fnp\Dto;

use Fnp\Dto\Exceptions\DtoClassNotExistsException;
use Fnp\Dto\Exceptions\DtoCouldNotAccessProperties;
use Fnp\ElHelper\Arr;
use Fnp\ElHelper\Flg;
use Fnp\ElHelper\Iof;
use Fnp\ElHelper\Obj;
use Illuminate\Support\Collection;
use ReflectionClass;
use ReflectionProperty;

class Dto
{
    public const PUBLIC                  = 0b000000000001;                // Fill only public properties
    public const PROTECTED               = 0b000000000010;                // Fill only protected properties
    public const PRIVATE                 = 0b000000000100;                // Fill only private properties
    public const EXCLUDE_NULLS           = 0b000000001000;                // Exclude values with NULL
    public const DONT_SERIALIZE_OBJECTS  = 0b000000010000;                // Do Not Serialize objects
    public const DONT_SERIALIZE_STRINGS  = 0b000000100000;                // Serialize objects with __toString
    public const PREFER_STRING_PROVIDERS = 0b000001000000;                // Prefer String Providers over Object Serialization
    public const JSON_PRETTY             = 0b000010000000;                // Produce nicely formatted JSON

    public static function collection(
        $modelClass,
        mixed $collection,
        int $flags = self::PUBLIC + self::PROTECTED + self::PRIVATE
    ): Collection {
        if (!$collection) {
            $collection = [];
        }

        if (!$modelClass || !class_exists($modelClass, true)) {
            throw DtoClassNotExistsException::make($modelClass);
        }

        if (Iof::arrayable($collection) && !Iof::collection($collection)) {
            $collection = $collection->toArray($flags);
        }

        if (!Iof::collection($collection)) {
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
     * @param  mixed   $attributes
     * @param  int     $flags
     *
     * @return object
     * @throws DtoCouldNotAccessProperties
     */
    public static function fill(
        object $model,
        mixed $attributes,
        int $flags = self::PRIVATE + self::PROTECTED + self::PUBLIC
    ): object {

        if (is_null($attributes)) {
            return $model;
        }

        if (!Arr::accessible($attributes) &&
            Iof::arrayable($attributes) &&
            !Iof::eloquentModel($attributes)) {
            $attributes = $attributes->toArray();
        }

        if ($attributes instanceof \stdClass) {
            $attributes = get_object_vars($attributes);
        }

        $vars = self::properties(
            $model,
            (Flg::has($flags, self::PRIVATE) ? ReflectionProperty::IS_PRIVATE : 0) +
            (Flg::has($flags, self::PROTECTED) ? ReflectionProperty::IS_PROTECTED : 0) +
            (Flg::has($flags, self::PUBLIC) ? ReflectionProperty::IS_PUBLIC : 0)
        );

        foreach ($vars as $variable) {
            $variable->setAccessible(true);
            $varName  = $variable->getName();
            $varValue = Arr::get($attributes, $varName, '!**NOTFOUND**__');

            if ($varValue === '!**NOTFOUND**__') {
                continue;
            }

            if (is_null($varValue) && Flg::has($flags, self::EXCLUDE_NULLS)) {
                continue;
            }

            $filler = Obj::methodExists($model, 'fill', $varName);

            if ($filler) {
                $varValue = $model->$filler($varValue);
                $variable->setValue($model, $varValue);
            } else {
                $variable->setValue($model, $varValue);
            }
        }

        return $model;
    }

    /**
     * @param  object  $model
     * @param  int     $filter
     *
     * @return ReflectionProperty[]
     * @throws DtoCouldNotAccessProperties
     */
    public static function properties(object $model, int $filter): array
    {
        try {
            $reflection = new ReflectionClass($model);
        } catch (\ReflectionException $e) {
            throw DtoCouldNotAccessProperties::make($model);
        }

        return $reflection->getProperties($filter);
    }

    /**
     * @param  object  $from
     * @param  object  $to
     * @param  object  $definition
     * @param  int     $flags
     *
     * @return object
     * @throws DtoCouldNotAccessProperties
     */
    public static function map(
        object $from,
        object $to,
        object $definition,
        int $flags = self::PUBLIC + self::PROTECTED + self::PRIVATE
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
     * @throws DtoCouldNotAccessProperties
     */
    public static function toArray(object $model, int $flags = self::PUBLIC + self::PROTECTED): array
    {
        $array = [];

        if (Iof::arrayable($model)) {
            $array = $model->toArray();
        } elseif (Iof::serializable($model)) {
            $array = $model->__serialize();
        } else {

            $vars = self::properties(
                $model,
                (Flg::has($flags, self::PRIVATE) ? ReflectionProperty::IS_PRIVATE : 0) +
                (Flg::has($flags, self::PROTECTED) ? ReflectionProperty::IS_PROTECTED : 0) +
                (Flg::has($flags, self::PUBLIC) ? ReflectionProperty::IS_PUBLIC : 0)
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
                    !Flg::has($flags, self::DONT_SERIALIZE_STRINGS) &&
                    Flg::has($flags, self::PREFER_STRING_PROVIDERS)
                ) {
                    $array[$varName] = $varValue->__toString();
                } elseif (
                    Iof::arrayable($varValue) &&
                    !Flg::has($flags, self::DONT_SERIALIZE_OBJECTS)
                ) {
                    $array[$varName] = $varValue->toArray();
                } elseif (
                    Iof::serializable($varValue) &&
                    !Flg::has($flags, self::DONT_SERIALIZE_OBJECTS)
                ) {
                    $array[$varName] = $varValue->__serialize();
                } elseif (
                    Iof::stringable($varValue) &&
                    !Flg::has($flags, self::DONT_SERIALIZE_STRINGS) &&
                    !Flg::has($flags, self::PREFER_STRING_PROVIDERS)
                ) {
                    $array[$varName] = $varValue->__toString();
                } else {
                    $array[$varName] = $varValue;
                }
            }
        }

        if (Flg::has($flags, self::EXCLUDE_NULLS)) {
            $array = array_filter($array, function ($value) {
                return !is_null($value);
            });
        }

        return $array;
    }

    /**
     * @param  object  $model
     * @param  int     $flags
     *
     * @return string
     * @throws DtoCouldNotAccessProperties
     */
    public static function toJson(object $model, int $flags): string
    {
        $array     = self::toArray($model, $flags);
        $jsonFlags = null;

        if (Flg::has($flags, self::JSON_PRETTY)) {
            $jsonFlags = JSON_PRETTY_PRINT;
        }

        return json_encode($array, $jsonFlags);
    }

    public static function serialize(object $model, int $flags = self::PUBLIC + self::PROTECTED): string
    {
        return ''; // TODO: To be implemented
    }

    public static function deserialize(string $model): object
    {
        return new static; // TODO: To be implemented
    }
}