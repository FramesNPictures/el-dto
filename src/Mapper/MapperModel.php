<?php

namespace Fnp\Dto\Mapper;

use Fnp\Dto\Collection\DtoCollectionFactory;
use Fnp\Dto\Common\Traits\DtoMapperFill;
use Fnp\Dto\Common\Traits\DtoToArray;
use Fnp\Dto\Exception\DtoClassNotExistsException;
use Fnp\Dto\Flex\DtoModel;
use Illuminate\Support\Collection;

class MapperModel extends DtoModel
{
    use DtoToArray;
    use DtoMapperFill;

    /**
     * Make model collection with initial data
     *
     * @param mixed      $items Data
     *
     * @param string     $key   For associative collection provide a model key
     * @param null|array $flags Flags
     *
     * @return Collection|null
     */
    public static function collection($items, $key = NULL, $flags = NULL)
    {
        try {
            return DtoCollectionFactory::make(get_called_class(), $items, $key, $flags);
        } catch (DtoClassNotExistsException $e) {
            return new Collection([]);
        }
    }
}