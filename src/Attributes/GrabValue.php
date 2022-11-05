<?php

namespace Fnp\Dto\Attributes;

use Attribute;
use Fnp\Dto\Contracts\ObtainsValue;
use Fnp\ElHelper\Arr;

#[Attribute]
class GrabValue implements ObtainsValue
{
    public function __construct(
        protected string $path,
        protected ?string $default = null,
    ) {
    }

    public function getValue(mixed $data): mixed
    {
        return Arr::get($data, $this->path, $this->default);
    }
}
