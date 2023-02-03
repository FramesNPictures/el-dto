<?php

use Fnp\Dto\Dto;
use PHPUnit\Framework\TestCase;

class DtoBasicFunctionalityTest extends TestCase
{
    public function provideFillData()
    {
        $simpleUserModel = new class {
            public           $name;
            public           $surname;
            protected string $email;
            private bool     $active = false;

            // Check if the properties are a match
            public function check(array $data)
            {
                foreach ($data as $prop => $value) {
                    if ($this->{$prop} !== $value) {
                        return false;
                    }
                }

                return true;
            }
        };

        $attributeModel = new class {
            #[\Fnp\Dto\Attributes\DtoValue('theName')]
            public           $name;
            #[\Fnp\Dto\Attributes\DtoValue('theSurname')]
            #[\Fnp\Dto\Attributes\DtoModifier('capitalize', '-test')]
            public           $surname;
            #[\Fnp\Dto\Attributes\DtoValue('theEmail')]
            #[\Fnp\Dto\Modifiers\DtoTrim()]
            #[\Fnp\Dto\Modifiers\DtoLowerCase()]
            protected string $email;
            #[\Fnp\Dto\Attributes\DtoValue('address')]
            #[\Fnp\Dto\Attributes\DtoSetter('setAddress', 1)]
            private string   $address1;
            #[\Fnp\Dto\Attributes\DtoValue('address')]
            #[\Fnp\Dto\Attributes\DtoSetter('setAddress', 2)]
            private string   $address2;
            #[\Fnp\Dto\Attributes\DtoValue('isActive')]
            #[\Fnp\Dto\Attributes\DtoDefaultValue(true)]
            private bool     $active = false;

            // Check if the properties are a match
            public function check(array $data)
            {
                foreach ($data as $prop => $value) {
                    if ($this->{$prop} !== $value) {
                        return false;
                    }
                }

                return true;
            }

            public function capitalize(string $value, string $extra): string
            {
                return strtoupper($value . $extra);
            }

            public function setAddress(string $address, int $line)
            {
                $lines                     = explode(',', $address);
                $this->{'address' . $line} = trim($lines[$line - 1]);
            }
        };

        return [
            'Simple Fill'             => [
                'mod' => clone $simpleUserModel,
                'dat' => [
                    'name'    => 'John',
                    'surname' => 'Doe',
                    'email'   => 'jd@gmail.com',
                    'active'  => true,
                ],
                'map' => null,
                'res' => [
                    'name'    => 'John',
                    'surname' => 'Doe',
                    'email'   => 'jd@gmail.com',
                    'active'  => true,
                ],
                'arr' => [
                    'name'    => 'John',
                    'surname' => 'Doe',
                    'email'   => 'jd@gmail.com',
                ],
            ],
            'Simple Fill Mapped'      => [
                'mod' => clone $simpleUserModel,
                'dat' => [
                    'theName'    => 'John',
                    'theSurname' => 'Doe',
                    'theEmail'   => 'jd@gmail.com',
                    'theActive'  => true,
                ],
                'map' => [
                    'name'    => 'theName',
                    'surname' => 'theSurname',
                    'email'   => 'theEmail',
                    'active'  => 'theActive',
                ],
                'res' => [
                    'name'    => 'John',
                    'surname' => 'Doe',
                    'email'   => 'jd@gmail.com',
                    'active'  => true,
                ],
                'arr' => [
                    'name'    => 'John',
                    'surname' => 'Doe',
                    'email'   => 'jd@gmail.com',
                ],
            ],
            'Mapped Multidimensional' => [
                'mod' => clone $simpleUserModel,
                'dat' => [
                    'userData' => [
                        'name' => 'John',
                        'surn' => 'Doe',
                        'mail' => 'jd@gmail.com',
                    ],
                    'userMeta' => [
                        'act' => true,
                    ],
                ],
                'map' => [
                    'name'    => 'userData.name',
                    'surname' => 'userData.surn',
                    'email'   => 'userData.mail',
                    'active'  => 'userMeta.act',
                ],
                'res' => [
                    'name'    => 'John',
                    'surname' => 'Doe',
                    'email'   => 'jd@gmail.com',
                    'active'  => true,
                ],
                'arr' => [
                    'name'    => 'John',
                    'surname' => 'Doe',
                    'email'   => 'jd@gmail.com',
                ],
            ],
            'With Attributes'         => [
                'mod' => clone $attributeModel,
                'dat' => [
                    'theName'    => 'John',
                    'theSurname' => 'Doe',
                    'theEmail'   => ' JD@gmail.COM',
                    'address'    => ' 10 High Street, London',
                ],
                'map' => null,
                'res' => [
                    'name'     => 'John',
                    'surname'  => 'DOE-TEST',          // Capitalized with modifier
                    'email'    => 'jd@gmail.com',      // @ replaced with % using EmailModifier class
                    'address1' => '10 High Street',
                    'address2' => 'London',
                    'active'   => true,
                ],
                'arr' => [
                    'name'    => 'John',
                    'surname' => 'DOE-TEST',          // Capitalized with modifier
                    'email'   => 'jd@gmail.com',      // @ replaced with % using EmailModifier class
                ],
            ],
        ];
    }

    /**
     * @test
     * @dataProvider provideFillData
     */
    public function testModelFillFunctionality($mod, $dat, $map, $res, $arr)
    {
        $model = Dto::fill($mod, $dat, $map);
        $this->assertTrue($mod->check($res), 'Results do not match.');
    }

    /**
     * @test
     * @dataProvider provideFillData
     */
    public function testConvertingToArrayFunctionality($mod, $dat, $map, $res, $arr)
    {
        $model = Dto::fill($mod, $dat, $map);
        $this->assertEquals($arr, Dto::toArray($mod), 'toArray results do not match.');
    }

    /**
     * @test
     * @dataProvider provideFillData
     */
    public function testConvertingToJsonFunctionality($mod, $dat, $map, $res, $arr)
    {
        $model = Dto::fill($mod, $dat, $map);
        $this->assertEquals($arr, json_decode(Dto::toJson($mod), true), 'toJson results do not match.');
    }
}
