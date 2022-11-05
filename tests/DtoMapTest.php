<?php

use Fnp\Dto\Dto;
use PHPUnit\Framework\TestCase;

class DtoMapTest
{
    public function provideTestData()
    {
        $from = new class() {
            public    $a = 'A';
            protected $b = 'B';
            private   $c = 'C';
            public    $d = [
                'd1' => 'One',
                'd2' => 'Two',
                'd3' => 'Three',
            ];
            public    $e = null;
        };

        $to = new class() {
            public    $x = 'X';
            protected $y = 'Y';
            private   $z = 'Z';
        };

        return [
            'Basic 1:1 map' => [
                clone $from,
                clone $to,
                new class() {
                    public $x = 'a';
                    public $y = 'b';
                    public $z = 'c';
                },
                Dto::INCLUDE_PRIVATE,
                [
                    'x' => 'A',
                    'y' => 'B',
                    'z' => 'C',
                ],
            ],
            'Basic 1:1 multidimensional map' => [
                clone $from,
                clone $to,
                new class() {
                    public $x = 'd.d1';
                    public $y = 'd.d2';
                    public $z = 'd.d3';
                },
                0,
                [
                    'x' => 'One',
                    'y' => 'Two',
                    'z' => 'Three',
                ],
            ],
            'Basic search map' => [
                clone $from,
                clone $to,
                new class() {
                    public $x = ['c', 'b', 'a'];
                    public $y = ['x', 'b'];
                    public $z = ['x', 'y', 'c'];
                },
                Dto::INCLUDE_PRIVATE,
                [
                    'x' => 'C',
                    'y' => 'B',
                    'z' => 'C',
                ],
            ],
            'Multidimensional search map' => [
                clone $from,
                clone $to,
                new class() {
                    public $x = ['d', 'b', 'a'];
                    public $y = ['x', 'd.d3'];
                    public $z = ['x', 'y', 'c'];
                },
                Dto::INCLUDE_PRIVATE,
                [
                    'x' => ['d1' => 'One', 'd2' => 'Two', 'd3' => 'Three'],
                    'y' => 'Three',
                    'z' => 'C',
                ],
            ],
            'Map including nulls' => [
                clone $from,
                clone $to,
                new class() {
                    public $x = 'a';
                    public $y = 'e';
                    public $z = 'a';
                },
                0,
                [
                    'x' => 'A',
                    'y' => null, // Default value should be overwritten by null
                    'z' => 'A',
                ],
            ],
            'Map excluding nulls' => [
                clone $from,
                clone $to,
                new class() {
                    public $x = 'a';
                    public $y = 'e';
                    public $z = 'a';
                },
                Dto::EXCLUDE_NULLS,
                [
                    'x' => 'A',
                    'y' => 'Y', // Excluding nulls so default Y value should be seen
                    'z' => 'A',
                ],
            ],
        ];
    }

    /**
     * @dataProvider provideTestData
     *
     * @param $from
     * @param $to
     * @param $definition
     * @param $flags
     * @param $result
     *
     * @throws \Fnp\Dto\Exceptions\DtoCouldNotAccessProperties
     */
    public function testDtoMap($from, $to, $definition, $flags, $result)
    {
        Dto::map($from, $to, $definition, $flags);
        $this->assertEquals(
            $result,
            Dto::toArray($to, Dto::INCLUDE_PRIVATE + Dto::INCLUDE_PUBLIC + Dto::INCLUDE_PROTECTED)
        );
    }
}
