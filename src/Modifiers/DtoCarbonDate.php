<?php

namespace Fnp\Dto\Modifiers;

use Attribute;
use Carbon\Carbon;
use Carbon\Exceptions\ParseErrorException;
use Fnp\Dto\Contracts\ModifiesDtoValue;

#[Attribute]
class DtoCarbonDate implements ModifiesDtoValue
{
    /**
     * @var null
     */
    private $timezone;

    public function __construct(string $timezone = null)
    {
        $this->timezone = $timezone;
    }

    public function modifyValue(mixed $value): mixed
    {
        try {
            return Carbon::parse($value, $this->timezone);
        } catch (ParseErrorException) {
            return null;
        }
    }
}
