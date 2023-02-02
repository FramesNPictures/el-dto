<?php

namespace Fnp\Dto\Attributes;

use Attribute;
use Fnp\Dto\Contracts\ObtainsValue;
use Fnp\ElHelper\Arr;

#[Attribute]
class DtoValue implements ObtainsValue
{
    protected $path;
    protected $default = null;

    public function __construct(
        $path,
        $default = null,
    ) {
        $this->path    = (array)$path;
        $this->default = $default;
    }

    public function getValue(mixed $data): mixed
    {
        $value = null;

        foreach($this->path as $path) {
            $value = Arr::get($data, $path);
            if (!is_null($value)) {
                return $value;
            }
        }

        return $this->default;
    }
}
