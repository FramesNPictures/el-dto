<?php

use Fnp\Dto\Dto;
use PHPUnit\Framework\TestCase;

class DtoFillTest extends TestCase
{
    public function provideFillData()
    {
        $simpleUserModel = new class {
            public           $name;
            public           $surname;
            protected string $email;
            protected bool   $active = false;

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
            ],
        ];
    }

    /**
     * @dataProvider provideFillData
     */
    public function testFillFunctionality($mod, $dat, $map, $res)
    {
        $model = Dto::fill($mod, $dat, $map);
        $this->assertTrue($mod->check($res), 'Results do not match.');
    }
}
