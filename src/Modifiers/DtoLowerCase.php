<?php

namespace Fnp\Dto\Modifiers;

use Attribute;
use Fnp\Dto\Contracts\ModifiesDtoValue;

#[Attribute]
class DtoLowerCase implements ModifiesDtoValue
{
    public function modifyValue(mixed $value): mixed
    {
        return strtolower($value);
    }
}
