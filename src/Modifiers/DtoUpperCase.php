<?php

namespace Fnp\Dto\Modifiers;

use Attribute;
use Fnp\Dto\Contracts\ModifiesDtoValue;

#[Attribute]
class DtoUpperCase implements ModifiesDtoValue
{
    public function modifyValue(mixed $value): mixed
    {
        return strtoupper($value);
    }
}
