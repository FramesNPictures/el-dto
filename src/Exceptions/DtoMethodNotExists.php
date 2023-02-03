<?php

namespace Fnp\Dto\Exceptions;

class DtoMethodNotExists extends DtoException
{
    public static function make(string $methodName)
    {
        return new static(
            sprintf('Could not find Model\'s method named "%s"', $methodName)
        );
    }
}
