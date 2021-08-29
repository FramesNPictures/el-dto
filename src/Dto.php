<?php

namespace Fnp\Dto;

use Fnp\Dto\Exceptions\DtoClassNotExistsException;
use Fnp\Dto\Exceptions\DtoCouldNotAccessProperties;
use Fnp\Dto\Flex\DtoModel;
use Fnp\ElHelper\Arr;
use Fnp\ElHelper\Flg;
use Fnp\ElHelper\Iof;
use Fnp\ElHelper\Obj;
use Illuminate\Support\Collection;
use ReflectionClass;
use ReflectionProperty;

class Dto
{
    const PUBLIC                     = 0b000000000001;                // Fill only public properties
    const PROTECTED                  = 0b000000000010;                // Fill only protected properties
    const PRIVATE                    = 0b000000000100;                // Fill only private properties
    const EXCLUDE_NULLS              = 0b000000001000;                // Exclude values with NULL
    const DONT_SERIALIZE_OBJECTS     = 0b000000010000;                // Do Not Serialize objects
    const SERIALIZE_STRING_PROVIDERS = 0b000000100000;                // Serialize objects with __toString
    const PREFER_STRING_PROVIDERS    = 0b000001000000;                // Prefer String Providers over Object Serialization
    const STRICT_MATCHING            = 0b000010000000;                // Strict property matching (no Camel <=> Snake)

    public static function collection($modelClass, mixed $collection, int $flags): Collection
    {
        if (!$collection) {
            $collection = [];
        }

        if (!$modelClass || !class_exists($modelClass, TRUE)) {
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

        // TODO: Use flags to decide which properties should be populated
        $vars = self::properties(
            $model,
            (Flg::has($flags, self::PRIVATE) ? ReflectionProperty::IS_PRIVATE : 0) +
            (Flg::has($flags, self::PROTECTED) ? ReflectionProperty::IS_PROTECTED : 0) +
            (Flg::has($flags, self::PUBLIC) ? ReflectionProperty::IS_PUBLIC : 0)
        );

        foreach ($vars as $variable) {
            $variable->setAccessible(true);
            $varName  = $variable->getName();
            $varValue = Arr::get($attributes, $varName);

            if (!is_null($varValue)) {
                $filler = Obj::methodExists($model, 'fill', $varName);

                if ($filler) {
                    $varValue = $model->$filler($varValue);
                    $variable->setValue($model, $varValue);
                } else {
                    $variable->setValue($model, $varValue);
                }
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
    protected static function properties(object $model, int $filter): array
    {
        try {
            $reflection = new ReflectionClass($model);
        } catch (\ReflectionException $e) {
            throw DtoCouldNotAccessProperties::make($model);
        }

        return $reflection->getProperties($filter);
    }

    public static function map(object $model, mixed $attributes, mixed $definitions): object
    {

    }

    public static function toArray(object $model, int $flags): array
    {

    }

    public static function toJson(object $model, int $flags): string
    {

    }

    public static function serialize(object $model): string
    {

    }

    public static function deserialize(string $model): object
    {

    }
}