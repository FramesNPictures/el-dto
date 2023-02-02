<?php

use Fnp\Dto\DtoLegacy;
use PHPUnit\Framework\TestCase;

class DtoReflectionTest extends TestCase
{
    public function provideReflectionFilterData()
    {
        return [
            'Default toArray + Include Private' => [
                DtoLegacy::INCLUDE_PUBLIC + DtoLegacy::INCLUDE_PROTECTED + DtoLegacy::EXCLUDE_PRIVATE,
                DtoLegacy::INCLUDE_PRIVATE,
                ReflectionProperty::IS_PUBLIC + ReflectionProperty::IS_PROTECTED + ReflectionProperty::IS_PRIVATE,
            ],
            'Default toArray + Exclude Protected' => [
                DtoLegacy::INCLUDE_PUBLIC + DtoLegacy::INCLUDE_PROTECTED + DtoLegacy::EXCLUDE_PRIVATE,
                DtoLegacy::EXCLUDE_PROTECTED,
                ReflectionProperty::IS_PUBLIC,
            ]
        ];
    }

    /**
     * @dataProvider provideReflectionFilterData
     *
     * @param $default
     * @param $flags
     * @param $result
     *
     * @throws ReflectionException
     */
    public function testReflectionFilter($default, $flags, $result)
    {
        $r = new ReflectionClass(DtoLegacy::class);
        $m = $r->getMethod('reflectionFilter');
        $m->setAccessible(true);

        $this->assertEquals($result, $m->invoke(null, $default, $flags));
    }
}
