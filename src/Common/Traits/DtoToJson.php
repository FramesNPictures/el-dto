<?php

namespace Fnp\Dto\Common\Traits;

trait DtoToJson
{
    abstract public function toArray($flags = NULL);

    /**
     * @inheritdoc
     *
     * @param int $options
     *
     * @return string
     */
    public function toJson($options = 0, $flags = NULL)
    {
        return json_encode($this->toArray($flags), $options);
    }
}