<?php

namespace Fnp\Dto\Common\Traits;

use Traversable;

trait DtoIteratorAggregate
{
    abstract public function toArray();

    /**
     * Retrieve an external iterator
     *
     * @link  http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return Traversable An instance of an object implementing <b>Iterator</b> or
     *        <b>Traversable</b>
     * @since 5.0.0
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->toArray());
    }
}