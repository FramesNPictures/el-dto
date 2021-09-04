<?php

use Fnp\Dto\Dto;
use PHPUnit\Framework\TestCase;

class DtoReflectionTest extends TestCase
{
    public function provideReflectionFilterData()
    {
        return [
            'Default toArray + Include Private' => [
                Dto::INCLUDE_PUBLIC + Dto::INCLUDE_PROTECTED + Dto::EXCLUDE_PRIVATE,
                Dto::INCLUDE_PRIVATE,
                ReflectionProperty::IS_PUBLIC + ReflectionProperty::IS_PROTECTED + ReflectionProperty::IS_PRIVATE,
            ],
            'Default toArray + Exclude Protected' => [
                Dto::INCLUDE_PUBLIC + Dto::INCLUDE_PROTECTED + Dto::EXCLUDE_PRIVATE,
                Dto::EXCLUDE_PROTECTED,
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
        $r = new ReflectionClass(Dto::class);
        $m = $r->getMethod('reflectionFilter');
        $m->setAccessible(true);

        $this->assertEquals($result, $m->invoke(null, $default, $flags));
    }
}