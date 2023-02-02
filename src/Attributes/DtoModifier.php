<?php

namespace Fnp\Dto\Attributes;

use Attribute;
use Fnp\Dto\Contracts\AccessesDtoModel;
use Fnp\Dto\Contracts\ModifiesDtoValue;

#[Attribute]
class DtoModifier implements ModifiesDtoValue, AccessesDtoModel
{
    protected mixed  $method;
    protected object $model;

    public function __construct(
        mixed $method
    ) {
        $this->method = $method;
    }

    public function setModel(object $model): void
    {
        $this->model = $model;
    }

    public function modifyValue(mixed $value): mixed
    {
        $model  = $this->model;
        $method = $this->method;

        if ($method instanceof AccessesDtoModel) {
            $method->setModel($model);
        }

        if ($method instanceof ModifiesDtoValue) {
            return $method->modifyValue($value);
        }

        return $model->$method($value);
    }
}
