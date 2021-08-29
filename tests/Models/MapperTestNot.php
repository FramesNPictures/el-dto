<?php

use Fnp\Dto\Flex\DtoModel;
use Fnp\Dto\Mapper\MapperModel;

class MapperTestNot extends \PHPUnit\Framework\TestCase
{
    public function generateUserData()
    {
        $faker = \Faker\Factory::create();
        $data  = [];

        for($i = 0; $i < 50; $i++) {
            $data[] = [
                $faker->userName,
                $faker->password,
            ];
        }

        return $data;
    }

    /**
     * @dataProvider generateUserData
     * @test
     */
    public function testMapping($userName, $password)
    {
        $source                   = new Source();
        $source->name_of_the_user = $userName;
        $source->user_password    = $password;

        $mapper = Mapper::make($source);

        $this->assertEquals($userName, $mapper->userName);
        $this->assertEquals($password, $mapper->password);

        $destination = Destination::make($mapper);

        $this->assertEquals($userName, $destination->userName);
        $this->assertEquals($password, $destination->password);
    }

    /**
     * @test
     */
    public function testNestedStructures()
    {
        $structure = [
            'one'   => 1,
            'two'   => [
                'a' => '1',
                'b' => '2',
                'c' => [
                    'x' => 101,
                    'y' => 202,
                    'z' => 303,
                ],
            ],
            'three' => 3,
        ];

        $mapper = NestedMapper::make($structure);

        $this->assertEquals(1, $mapper->getOne());
        $this->assertEquals('2', $mapper->getTwo());
        $this->assertEquals(303, $mapper->getThree());
    }
}

class Mapper extends MapperModel
{
    public $userName = 'name_of_the_user';
    public $password = 'user_password';
}

class Destination extends DtoModel
{
    public $userName;
    public $password;
}

class NestedMapper extends \Fnp\Dto\Mapper\MapperModel
{
    protected $one = 'one';
    protected $two = 'two.b';
    protected $three = 'two.c.z';

    /**
     * @return string
     */
    public function getOne()
    {
        return $this->one;
    }

    /**
     * @return string
     */
    public function getTwo()
    {
        return $this->two;
    }

    /**
     * @return string
     */
    public function getThree()
    {
        return $this->three;
    }
}

class Source extends DtoModel
{
    public $name_of_the_user;
    public $user_password;
}