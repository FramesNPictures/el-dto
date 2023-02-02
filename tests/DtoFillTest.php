<?php

use Fnp\Dto\DtoLegacy;

class DtoFillTest
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
                DtoLegacy::INCLUDE_PUBLIC + DtoLegacy::INCLUDE_PROTECTED + DtoLegacy::INCLUDE_PRIVATE,
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
                DtoLegacy::INCLUDE_PROTECTED + DtoLegacy::EXCLUDE_PUBLIC + DtoLegacy::EXCLUDE_PRIVATE,
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
                DtoLegacy::INCLUDE_PRIVATE + DtoLegacy::EXCLUDE_PUBLIC + DtoLegacy::EXCLUDE_PROTECTED,
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
                DtoLegacy::INCLUDE_PUBLIC + DtoLegacy::EXCLUDE_PROTECTED + DtoLegacy::EXCLUDE_PRIVATE,
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
                DtoLegacy::EXCLUDE_PUBLIC + DtoLegacy::EXCLUDE_PRIVATE,
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
                DtoLegacy::EXCLUDE_PUBLIC + DtoLegacy::EXCLUDE_PROTECTED,
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
                DtoLegacy::EXCLUDE_PROTECTED + DtoLegacy::EXCLUDE_PRIVATE,
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
                DtoLegacy::EXCLUDE_PROTECTED + DtoLegacy::EXCLUDE_PRIVATE,
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
                DtoLegacy::EXCLUDE_PROTECTED + DtoLegacy::EXCLUDE_PRIVATE + DtoLegacy::EXCLUDE_NULLS,
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
     * @throws \Fnp\ElHelper\Exceptions\CouldNotAccessProperties
     */
    public function testFillingModel($model, $data, $flags, $result)
    {
        DtoLegacy::fill($model, $data, $flags);
        $this->assertEquals($result, $model->toArray());
    }
}
