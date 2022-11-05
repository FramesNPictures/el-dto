<?php

namespace Fnp\Dto\Attributes;

use Attribute;
use Fnp\Dto\Contracts\AccessesModel;
use Fnp\Dto\Contracts\ModifiesValue;

#[Attribute]
class UseModifier implements ModifiesValue, AccessesModel
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

        if ($method instanceof AccessesModel) {
            $method->setModel($model);
        }

        if ($method instanceof ModifiesValue) {
            return $method->modifyValue($value);
        }

        return $model->$method($value);
    }
}
