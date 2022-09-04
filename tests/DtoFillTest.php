<?php

use Fnp\Dto\Dto;
use PHPUnit\Framework\TestCase;

class DtoFillTest extends TestCase
{
    /**
     * @return array[]
     * @todo Add additional acceptable attribute types
     */
    public function provideFillData()
    {
        $basicModel = new class() {
            public    $pub = null;
            protected $pro = null;
            private   $pri = null;

            public function toArray()
            {
                return [
                    'pub' => $this->pub,
                    'pro' => $this->pro,
                    'pri' => $this->pri,
                ];
            }
        };

        $fillModel = new class() {
            public    $pub = 'pub';
            protected $pro = null;
            private   $pri = null;

            public function fillPub($value)
            {
                return 'PubFill '.$value;
            }

            public function fillPro($value)
            {
                return 'ProFill '.$value;
            }

            public function fillPri($value)
            {
                return 'PriFill '.$value;
            }

            public function fillTst($value)
            {
                return $value;
            }

            public function toArray()
            {
                return [
                    'pub' => $this->pub,
                    'pro' => $this->pro,
                    'pri' => $this->pri,
                ];
            }
        };

        return [

            /*
             * Basic Fill (Array)
             * ----------
             * Whenever property name and array key matches
             * value should be assigned
             */
            'Array All' => [
                clone $basicModel,
                [
                    'pub' => 'Public Property',
                    'pro' => 'Protected Property',
                    'pri' => 'Private Property',
                    'pru' => 'Should not be assigned',
                ],
                Dto::INCLUDE_PUBLIC + Dto::INCLUDE_PROTECTED + Dto::INCLUDE_PRIVATE,
                [
                    'pub' => 'Public Property',
                    'pro' => 'Protected Property',
                    'pri' => 'Private Property',
                ],
            ],
            'Array Protected' => [
                clone $basicModel,
                [
                    'pub' => 'Public Property',
                    'pro' => 'Protected Property',
                    'pri' => 'Private Property',
                    'prg' => 'Should not be assigned'
                ],
                Dto::INCLUDE_PROTECTED + Dto::EXCLUDE_PUBLIC + Dto::EXCLUDE_PRIVATE,
                [
                    'pub' => null,
                    'pro' => 'Protected Property',
                    'pri' => null,
                ],
            ],
            'Array Private' => [
                clone $basicModel,
                [
                    'pub' => 'Public Property',
                    'pro' => 'Protected Property',
                    'pri' => 'Private Property',
                ],
                Dto::INCLUDE_PRIVATE + Dto::EXCLUDE_PUBLIC + Dto::EXCLUDE_PROTECTED,
                [
                    'pub' => null,
                    'pro' => null,
                    'pri' => 'Private Property',
                ],
            ],
            'Array Public' => [
                clone $basicModel,
                [
                    'pub' => 'Public Property',
                    'pro' => 'Protected Property',
                    'pri' => 'Private Property',
                ],
                Dto::INCLUDE_PUBLIC + Dto::EXCLUDE_PROTECTED + Dto::EXCLUDE_PRIVATE,
                [
                    'pub' => 'Public Property',
                    'pro' => null,
                    'pri' => null,
                ],
            ],

            /* Advanced Fill (Array)
             * -------------
             * If property name and key matches and
             * fill method exists result of fill method execution
             * should be filled to property.
             */

            'Fill Array All' => [
                clone $fillModel,
                [
                    'pub' => 'Public Property',
                    'pro' => 'Protected Property',
                    'pri' => 'Private Property',
                    'tst' => 'Should not be filled',
                ],
                0, // Default behaviour
                [
                    'pub' => 'PubFill Public Property',
                    'pro' => 'ProFill Protected Property',
                    'pri' => 'PriFill Private Property',
                ],
            ],
            'Fill Array Protected' => [
                clone $fillModel,
                [
                    'pub' => 'Public Property',
                    'pro' => 'Protected Property',
                    'pri' => 'Private Property',
                ],
                Dto::EXCLUDE_PUBLIC + Dto::EXCLUDE_PRIVATE,
                [
                    'pub' => 'pub',
                    'pro' => 'ProFill Protected Property',
                    'pri' => null,
                ],
            ],
            'Fill Array Private' => [
                clone $fillModel,
                [
                    'pub' => 'Public Property',
                    'pro' => 'Protected Property',
                    'pri' => 'Private Property',
                ],
                Dto::EXCLUDE_PUBLIC + Dto::EXCLUDE_PROTECTED,
                [
                    'pub' => 'pub',
                    'pro' => null,
                    'pri' => 'PriFill Private Property',
                ],
            ],
            'Fill Array Public' => [
                clone $fillModel,
                [
                    'pub' => 'Public Property',
                    'pro' => 'Protected Property',
                    'pri' => 'Private Property',
                ],
                Dto::EXCLUDE_PROTECTED + Dto::EXCLUDE_PRIVATE,
                [
                    'pub' => 'PubFill Public Property',
                    'pro' => null,
                    'pri' => null,
                ],
            ],

            /*
             * Include & Exclude Nulls
             */
            'Fill Including Nulls' => [
                clone $fillModel,
                [
                    'pub' => null,
                ],
                Dto::EXCLUDE_PROTECTED + Dto::EXCLUDE_PRIVATE,
                [
                    'pub' => 'PubFill ',
                    'pro' => null,
                    'pri' => null,
                ],
            ],
            'Fill Excluding Nulls' => [
                clone $fillModel,
                [
                    'pub' => null,
                ],
                Dto::EXCLUDE_PROTECTED + Dto::EXCLUDE_PRIVATE + Dto::EXCLUDE_NULLS,
                [
                    'pub' => 'pub',
                    'pro' => null,
                    'pri' => null,
                ],
            ],
        ];
    }

    /**
     * @dataProvider provideFillData
     *
     * @param $model
     * @param $data
     * @param $flags
     * @param $result
     *
     * @throws \Fnp\Dto\Exceptions\DtoCouldNotAccessProperties
     */
    public function testFillingModel($model, $data, $flags, $result)
    {
        Dto::fill($model, $data, $flags);
        $this->assertEquals($result, $model->toArray());
    }
}