<?php

namespace Fnp\Dto\Set;

use Fnp\Dto\Common\Traits\DtoFill;

abstract class PersistenceSetModel extends SetModel
{
    use DtoFill;

    protected function build($handle)
    {
        $data = $this->retrieve($handle);
        $this->fill($data);
    }

    /**
     * Use this method to retrieve a record with the given handle.
     * Throw an exception if not found.
     * Caching can be done here as well.
     *
     * @param $handle
     *
     * @return mixed
     */
    abstract protected function retrieve($handle);

}