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

    }
}