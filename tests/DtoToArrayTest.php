<?php

use Fnp\Dto\Dto;
use PHPUnit\Framework\TestCase;

class DtoToArrayTest extends TestCase
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
                Dto::PUBLIC + Dto::PROTECTED + Dto::PRIVATE,
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
                Dto::PUBLIC,
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
                Dto::PROTECTED,
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
                Dto::PRIVATE,
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
                Dto::PUBLIC,
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
                Dto::PUBLIC + Dto::EXCLUDE_NULLS,
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
                Dto::PUBLIC,
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
                Dto::PUBLIC + Dto::DONT_SERIALIZE_OBJECTS,
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
                Dto::PUBLIC,
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
                Dto::PUBLIC + Dto::DONT_SERIALIZE_OBJECTS,
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
                Dto::PUBLIC,
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
                Dto::PUBLIC + Dto::DONT_SERIALIZE_STRINGS,
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
                Dto::PUBLIC + Dto::PREFER_STRING_PROVIDERS,
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
                Dto::PUBLIC,
                [
                    'a' => 'A',
                    'b' => ['c' => 'C', 'd' => 'D'],
                    'c' => 'C',
                ],
            ],
            'From toArray()' => [
                clone $classArrayable,
                [],
                Dto::PUBLIC,
                [
                    'c'=>'C',
                    'd'=>'D',
                ],
            ],
            'From __serialize()' => [
                clone $classSerializable,
                [],
                Dto::PUBLIC,
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
     * @throws \Fnp\Dto\Exceptions\DtoCouldNotAccessProperties
     */
    public function testDtoToArray(object $model, array $data, int $flags, array $result)
    {
        Dto::fill($model, $data);
        $this->assertEquals($result, Dto::toArray($model, $flags));
    }
}