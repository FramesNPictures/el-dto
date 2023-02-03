<?php

namespace Fnp\Dto\Attributes;

use Attribute;
use Fnp\Dto\Contracts\AccessesDtoModel;
use Fnp\Dto\Contracts\ModifiesDtoValue;
use Fnp\Dto\Exceptions\DtoMethodNotExists;

#[Attribute]
class DtoModifier implements ModifiesDtoValue, AccessesDtoModel
{
    protected mixed  $method;
    protected mixed  $data;
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

    public function modifyValue(mixed $value): mixed
    {
        $model  = $this->model;
        $method = $this->method;

        if ( ! method_exists($this->model, $this->method)) {
            throw DtoMethodNotExists::make($this->method);
        }

        return $model->$method($value, ...$this->params);
    }
}
