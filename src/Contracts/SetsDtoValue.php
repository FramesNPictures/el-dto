<?php

namespace Fnp\Dto\Contracts;

interface SetsDtoValue
{
    /**
     * Sets the property value with the given one.
     *
     * @param  mixed  $value
     *
     * @return void
     */
    public function setValue(mixed $value): void;
}
