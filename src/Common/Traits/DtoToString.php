<?php

namespace Fnp\Dto\Common\Traits;

trait DtoToString
{
    abstract public function toJson($options = 0);

    public function __toString()
    {
        return $this->toJson();
    }
}