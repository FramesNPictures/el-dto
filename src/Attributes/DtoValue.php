<?php

namespace Fnp\Dto\Attributes;

use Attribute;
use Fnp\Dto\Contracts\ReturnsValue;
use Fnp\ElHelper\Arr;

#[Attribute]
class DtoValue implements ReturnsValue
{
    protected array $path;
    protected mixed $default = null;

    public function __construct(
        string $path,
        mixed $default = null,
    ) {
        $this->path    = (array) $path;
        $this->default = $default;
    }

    public function getValue(mixed $data): mixed
    {
        foreach ($this->path as $path) {
            $value = Arr::get($data, $path);
            if ( ! is_null($value)) {
                return $value;
            }
        }

        return $this->default;
    }
}
