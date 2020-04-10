<?php

use Illuminate\Support\Collection;

class CollectionFactoryTest extends \PHPUnit\Framework\TestCase
{
    public function testCollection()
    {
        $data = [
            [
                'one'   => 'one',
                'two'   => 'two',
                'three' => 'three',
            ],
            [
                'one'   => 'a',
                'two'   => 'b',
                'three' => 'c',
            ],
            [
                'one'   => 'x',
                'two'   => 'y',
                'three' => 'z',
            ],
            [
                'one'   => 'joe',
                'two'   => 'jane',
                'three' => 'james',
            ],
        ];

        $c = CollectionFactoryTestModel::collection($data);

        $this->assertEquals(4, $c->count(), 'Should have three elements');

        return $c;
    }

    /**
     * @depends testCollection
     * @param Collection $c
     */
    public function testKeyedCollection(Collection $c)
    {
        $k = CollectionFactoryTestModel::collection($c, 'one');

        $this->assertTrue($k->has('one'));
        $this->assertTrue($k->has('a'));
        $this->assertTrue($k->has('x'));
        $this->assertTrue($k->has('joe'));

        $this->assertFalse($k->has('two'));
        $this->assertFalse($k->has('b'));
        $this->assertFalse($k->has('y'));
        $this->assertFalse($k->has('jane'));
    }
}

class CollectionFactoryTestModel extends \Fnp\Dto\Flex\DtoModel
{
    public $one;
    public $two;
    public $three;
}