<?php

namespace Fnp\Dto\Contracts;

interface ModifiesValue
{
    /**
     * Modifies provided value, provides a conversion
     * between the values.
     *
     * @param  mixed  $value
     *
     * @return mixed
     */
    public function modifyValue(mixed $value): mixed;
}
