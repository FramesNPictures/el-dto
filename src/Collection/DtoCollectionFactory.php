<?php

namespace Fnp\Dto\Collection;

use Fnp\ElHelper\Iof;
use Fnp\Dto\Exceptions\DtoClassNotExistsException;
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

    }
}