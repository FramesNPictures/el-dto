<?php

namespace Fnp\Dto\Attributes;

use Attribute;
use Fnp\Dto\Contracts\ModifiesDtoValue;

#[Attribute]
class DtoDefaultValue implements ModifiesDtoValue
{
    private mixed $value;

    /**
     * @param  mixed  $value
     */
    public function __construct(mixed $value)
    {
        $this->value = $value;
    }

    public function modifyValue(mixed $value): mixed
    {
        if ( ! is_null($value)) {
            return $value;
        }

        return $this->value;
    }
}
