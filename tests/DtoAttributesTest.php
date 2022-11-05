<?php

use Fnp\Dto\Attributes\GrabValue;
use Fnp\Dto\Attributes\UseModifier;
use Fnp\Dto\Contracts\ModifiesValue;
use Fnp\Dto\Dto;
use PHPUnit\Framework\TestCase;

class DtoAttributesTest extends TestCase
{
    public function testValueAttribute()
    {
        $obj = new AttrModelA;
        $obj = Dto::fill(
            $obj,
            [
                'aa' => 'aa',
                'bb' => 'bb',
                'cc' => 'cc',
                'ee' => [
                    'one' => 'one',
                    'two' => 'two',
                ],
            ]);
        dd(Dto::toArray($obj));
    }
}

class AttrModelA
{
    #[GrabValue('aa')]
    public string $a;

    #[GrabValue('bb')]
    public string $b;

    #[GrabValue('cc')]
    #[UseModifier('capitalize')]
    public string $c;

    #[GrabValue('dd', default: 'NoNe')]
    #[UseModifier(new ModifierA())]
    public string $d;

    #[GrabValue('ee.one', 'None')]
    #[UseModifier('capitalize')]
    public string $e;

    public function capitalize(string $value): string
    {
        return strtoupper($value);
    }
}

class ModifierA implements ModifiesValue
{
    public function modifyValue(mixed $value): mixed
    {
        return strtolower($value);
    }
}
