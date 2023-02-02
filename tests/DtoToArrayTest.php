<?php

use Fnp\Dto\DtoLegacy;
use Illuminate\Support\Collection;

class DtoToArrayTest
{
    public function provideTestData()
    {
        $modelA = new class() {
            public    $pub;
            protected $pro;
            private   $pri;
        };

        $modelB = new class() {
            public $a;
            public $b;
            public $c;
        };

        $classArrayable = new class {
            public function toArray()
            {
                return ['c' => 'C', 'd' => 'D'];
            }
        };

        $classSerializable = new class {
            public function __serialize()
            {
                return ['c' => 'C', 'd' => 'D'];
            }
        };

        $classStringable = new class {
            public function __toString()
            {
                return 'STRING';
            }
        };

        $classStringableAndSerializable = new class {
            public function __toString()
            {
                return 'STRING';
            }

            public function __serialize()
            {
                return ['c' => 'C', 'd' => 'D'];
            }
        };

        return [
            'All Property Visibility' => [
                clone $modelA,
                [
                    'pub' => 'Public',
                    'pro' => 'Protected',
                    'pri' => 'Private',
                ],
                DtoLegacy::INCLUDE_PUBLIC + DtoLegacy::INCLUDE_PROTECTED + DtoLegacy::INCLUDE_PRIVATE,
                [
                    'pub' => 'Public',
                    'pro' => 'Protected',
                    'pri' => 'Private',
                ],
            ],
            'Public Only' => [
                clone $modelA,
                [
                    'pub' => 'Public',
                    'pro' => 'Protected',
                    'pri' => 'Private',
                ],
                DtoLegacy::INCLUDE_PUBLIC + DtoLegacy::EXCLUDE_PROTECTED + DtoLegacy::EXCLUDE_PRIVATE,
                [
                    'pub' => 'Public',
                ],
            ],
            'Protected Only' => [
                clone $modelA,
                [
                    'pub' => 'Public',
                    'pro' => 'Protected',
                    'pri' => 'Private',
                ],
                DtoLegacy::INCLUDE_PROTECTED + DtoLegacy::EXCLUDE_PUBLIC + DtoLegacy::EXCLUDE_PRIVATE,
                [
                    'pro' => 'Protected',
                ],
            ],
            'Private Only' => [
                clone $modelA,
                [
                    'pub' => 'Public',
                    'pro' => 'Protected',
                    'pri' => 'Private',
                ],
                DtoLegacy::INCLUDE_PRIVATE + DtoLegacy::EXCLUDE_PUBLIC + DtoLegacy::EXCLUDE_PROTECTED,
                [
                    'pri' => 'Private',
                ],
            ],
            'Include Nulls' => [
                clone $modelB,
                [
                    'a' => 'A',
                    'b' => null,
                    'c' => 'C',
                ],
                0,
                [
                    'a' => 'A',
                    'b' => null,
                    'c' => 'C',
                ],
            ],
            'Exclude Nulls' => [
                clone $modelB,
                [
                    'a' => 'A',
                    'b' => null,
                    'c' => 'C',
                ],
                DtoLegacy::EXCLUDE_NULLS,
                [
                    'a' => 'A',
                    'c' => 'C',
                ],
            ],
            'Serialize Objects Arrayable' => [
                clone $modelB,
                [
                    'a' => 'A',
                    'b' => $classArrayable,
                    'c' => 'C',
                ],
                0,
                [
                    'a' => 'A',
                    'b' => ['c' => 'C', 'd' => 'D'],
                    'c' => 'C',
                ],
            ],
            'DO NOT Serialize Objects Arrayable' => [
                clone $modelB,
                [
                    'a' => 'A',
                    'b' => $classArrayable,
                    'c' => 'C',
                ],
                DtoLegacy::DONT_SERIALIZE_OBJECTS,
                [
                    'a' => 'A',
                    'b' => $classArrayable,
                    'c' => 'C',
                ],
            ],
            'Serialize Objects Serializable' => [
                clone $modelB,
                [
                    'a' => 'A',
                    'b' => $classSerializable,
                    'c' => 'C',
                ],
                0,
                [
                    'a' => 'A',
                    'b' => ['c' => 'C', 'd' => 'D'],
                    'c' => 'C',
                ],
            ],
            'DO NOT Serialize Objects Serializable' => [
                clone $modelB,
                [
                    'a' => 'A',
                    'b' => $classSerializable,
                    'c' => 'C',
                ],
                DtoLegacy::DONT_SERIALIZE_OBJECTS,
                [
                    'a' => 'A',
                    'b' => $classSerializable,
                    'c' => 'C',
                ],
            ],
            'Serialize Strings Providers' => [
                clone $modelB,
                [
                    'a' => 'A',
                    'b' => $classStringable,
                    'c' => 'C',
                ],
                0,
                [
                    'a' => 'A',
                    'b' => 'STRING',
                    'c' => 'C',
                ],
            ],
            'DO NOT Serialize String Providers' => [
                clone $modelB,
                [
                    'a' => 'A',
                    'b' => $classStringable,
                    'c' => 'C',
                ],
                DtoLegacy::DONT_SERIALIZE_STRINGS,
                [
                    'a' => 'A',
                    'b' => $classStringable,
                    'c' => 'C',
                ],
            ],
            'Prefer String Providers' => [
                clone $modelB,
                [
                    'a' => 'A',
                    'b' => $classStringableAndSerializable,
                    'c' => 'C',
                ],
                DtoLegacy::PREFER_STRING_PROVIDERS,
                [
                    'a' => 'A',
                    'b' => 'STRING',
                    'c' => 'C',
                ],
            ],
            'Prefer Serialize Providers' => [
                clone $modelB,
                [
                    'a' => 'A',
                    'b' => $classStringableAndSerializable,
                    'c' => 'C',
                ],
                0,
                [
                    'a' => 'A',
                    'b' => ['c' => 'C', 'd' => 'D'],
                    'c' => 'C',
                ],
            ],
            'From toArray()' => [
                clone $classArrayable,
                [],
                DtoLegacy::INCLUDE_PUBLIC,
                [
                    'c'=>'C',
                    'd'=>'D',
                ],
            ],
            'From __serialize()' => [
                clone $classSerializable,
                [],
                DtoLegacy::INCLUDE_PUBLIC,
                [
                    'c'=>'C',
                    'd'=>'D',
                ],
            ],
        ];
    }

    /**
     * @dataProvider provideTestData
     * @test
     *
     * @param  object  $model
     * @param  array   $data
     * @param  int     $flags
     * @param  array   $result
     *
     * @throws \Fnp\ElHelper\Exceptions\CouldNotAccessProperties
     */
    public function testDtoToArray(object $model, array $data, int $flags, array $result)
    {
        DtoLegacy::fill($model, $data);
        $this->assertEquals($result, DtoLegacy::toArray($model, $flags));
    }

    public function testDtoToArrayCollection()
    {
        $data = [
            new class {
                public $a = 'A';
                public $b = 'B';
                public $c = 'C';
            },
            new class {
                public $a = 1;
                public $b = 2;
                public $c = 3;
            },
            new class {
                public $a = 'A';
                public $b = 'B';
                public $c = 'C';
            },
        ];

        $collection = new Collection($data);
        $this->assertEquals(
            [
                ['a' => 'A', 'b' => 'B', 'c' => 'C'],
                ['a' => 1, 'b' => 2, 'c' => 3],
                ['a' => 'A', 'b' => 'B', 'c' => 'C'],
            ],
            DtoLegacy::toArray($collection)
        );
    }

    public function testDtoToArrayCollectionDeepSerialization()
    {
        $secondLevel = new class {
            public $x = 'X';
            public $y = 'Y';
            public $z = 'Z';
        };
        $data        = [
            new class {
                public $a = 'A';
                public $b = 'B';
                public $c = 'C';
            },
            new class {
                public $a = 1;
                public $b = 2;
                public $c = 3;
            },
        ];
        $data[0]->c  = clone $secondLevel;
        $data[1]->c  = clone $secondLevel;

        $collection = new Collection($data);
        $this->assertEquals(
            [
                ['a' => 'A', 'b' => 'B', 'c' => ['x' => 'X', 'y' => 'Y', 'z' => 'Z']],
                ['a' => 1, 'b' => 2, 'c' => ['x' => 'X', 'y' => 'Y', 'z' => 'Z']],
            ],
            DtoLegacy::toArray($collection)
        );
    }
}
