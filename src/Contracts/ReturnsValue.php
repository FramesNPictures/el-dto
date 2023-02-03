<?php

namespace Fnp\Dto\Contracts;

interface ReturnsValue
{
    /**
     * Obtains the value from the provided single
     * or multidimensional array, object or model
     *
     * @param  mixed  $data
     *
     * @return mixed
     */
    public function getValue(mixed $data): mixed;
}
