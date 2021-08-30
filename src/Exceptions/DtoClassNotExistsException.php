<?php

namespace Fnp\Dto\Exceptions;

class DtoClassNotExistsException extends DtoException
{
    public static function make($dtoClassName)
    {
        if (!$dtoClassName) {
            return new self('Dto class name cannot be NULL.');  
        }
        
        return new self(
            sprintf('Dto class %s not exists.', $dtoClassName)
        );
    }
}