<?php

namespace Fnp\Dto\Attributes;

use Attribute;
use Fnp\Dto\Contracts\AccessesDtoModel;
use Fnp\Dto\Contracts\SetsDtoValue;

#[Attribute]
class DtoSetter implements AccessesDtoModel, SetsDtoValue
{
    protected mixed  $method;
    protected object $model;
    protected array  $params;

    public function __construct(
        mixed $method,
        ...$params
    ) {
        $this->method = $method;
        $this->params = $params;
    }

    public function setModel(object $model): void
    {
        $this->model = $model;
    }

    public function setValue(mixed $value): void
    {
        $model  = $this->model;
        $method = $this->method;

        $model->$method($value, ...$this->params);
    }
}
