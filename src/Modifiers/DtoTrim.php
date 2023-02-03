<?php

namespace Fnp\Dto\Modifiers;
use Attribute;
use Fnp\Dto\Contracts\ModifiesDtoValue;

#[Attribute]
class DtoTrim implements ModifiesDtoValue
{
    private ?string $characters;

    public function __construct(string $characters = null)
    {
        $this->characters = $characters;
    }

    public function modifyValue(mixed $value): mixed
    {
        if ($this->characters) {
            return trim($value, $this->characters);
        }

        return trim($value);
    }
}
