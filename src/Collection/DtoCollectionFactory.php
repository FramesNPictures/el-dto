<?php

namespace Fnp\Dto\Collection;

use Fnp\Dto\Common\Helper\Iof;
use Fnp\Dto\Exception\DtoClassNotExistsException;
use Fnp\Dto\Flex\DtoModel;
use Illuminate\Support\Collection;

/**
 * DTO Collection Factory Class
 *
 * @package Fnp\Dto
 */
class DtoCollectionFactory
{
    /**
     * Converts existing collection to use models of a given class
     * or creates a new one.
     *
     * @param string                 $dtoClass
     * @param Collection|array|mixed $collection
     * @param null                   $key
     * @param null                   $flags
     *
     * @return Collection|null
     * @throws DtoClassNotExistsException
     */
    public static function make($dtoClass, $collection, $key = NULL, $flags = NULL)
    {
        if (!$collection) {
            $collection = [];
        }

        if (!$dtoClass || !class_exists($dtoClass, TRUE)) {
            throw DtoClassNotExistsException::make($dtoClass);
        }

        if ($collection instanceof \stdClass) {
            $collection = get_object_vars($collection);
        }

        if (Iof::arrayable($collection) && !Iof::collection($collection)) {
            $collection = $collection->toArray($flags);
        }

        if (!Iof::collection($collection)) {
            $collection = new Collection($collection);
        }

        $collection = $collection->map(function ($item, $key) use ($dtoClass, $flags) {
            /** @var DtoModel $dtoClass */
            return $dtoClass::make($item, $flags);
        });

        if ($key)
            $collection = $collection->pipe(function($c) use ($key) {
                $keyable = new Collection();
                $c->each(function($m) use ($keyable, $key) {
                    $keyable->put($m->$key, $m);
                });
                return $keyable;
            });

        return $collection;
    }
}