<?php

namespace Fnp\Dto\Attributes;

use Attribute;
use Fnp\Dto\Contracts\ModifiesDtoValue;
use Fnp\Dto\Dto;

#[Attribute]
class DtoModel implements ModifiesDtoValue
{
    protected string $modelClassName;

    /**
     * @param  string  $modelClassName
     */
    public function __construct(string $modelClassName)
    {
        $this->modelClassName = $modelClassName;
    }

    public function modifyValue(mixed $value): mixed
    {
        $model = new $this->modelClassName;
        Dto::fill($model, $value);

        return $model;
    }
}
