<?php

namespace Fnp\Dto\Exceptions;

class DtoCouldNotAccessProperties extends DtoException
{
    public static function make(object $model)
    {
        return new static(
            sprintf(
                'Could not access properties of model %s',
                get_class($model)
            )
        );
    }
}