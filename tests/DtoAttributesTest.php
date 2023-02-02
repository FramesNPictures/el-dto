<?php

use Fnp\Dto\Attributes\DtoValue;
use Fnp\Dto\Attributes\DtoModifier;
use Fnp\Dto\Contracts\ModifiesDtoValue;
use Fnp\Dto\DtoLegacy;
use PHPUnit\Framework\TestCase;

class DtoAttributesTest extends TestCase
{
    public function testValueAttribute()
    {
        $obj = new AttrModelA;
        $obj = DtoLegacy::fill(
            $obj,
            [
                'aa' => 'aa',
                'bb' => 'bb',
                'cc' => 'cc',
                'ee' => [
                    'one' => 'one',
                    'two' => 'two',
                ],
                'gg' => 'gg',
            ]);
        dd(DtoLegacy::toArray($obj));
    }
}

class AttrModelA
{
    #[DtoValue('aa')]
    public string $a;

    #[DtoValue('bb')]
    public string $b;

    #[DtoValue('cc')]
    #[DtoModifier('capitalize')]
    public string $c;

    #[DtoValue('dd', default: 'NoNe')]
    #[DtoModifier(new ModifierA())]
    public string $d;

    #[DtoValue('ee.one', 'None')]
    #[DtoModifier('capitalize')]
    public string $e;

    #[DtoValue(['ff', 'gg'], 'None')]
    public string $f;

    public function capitalize(string $value): string
    {
        return strtoupper($value);
    }
}

class ModifierA implements ModifiesDtoValue
{
    public function modifyValue(mixed $value): mixed
    {
        return strtolower($value);
    }
}
