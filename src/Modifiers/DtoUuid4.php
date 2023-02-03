<?php

namespace Fnp\Dto\Modifiers;

use Attribute;
use Fnp\Dto\Contracts\ModifiesDtoValue;
use Ramsey\Uuid\Uuid;

#[Attribute]
class DtoUuid4 implements ModifiesDtoValue
{
    public function modifyValue(mixed $value): mixed
    {
        if (!is_null($value)) {
            return $value;
        }

        return Uuid::uuid4();
    }
}
