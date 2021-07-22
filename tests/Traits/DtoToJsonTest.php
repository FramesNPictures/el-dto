<?php

use Fnp\Dto\Common\Flags\DtoToArrayFlags;
use Fnp\ElHelper\Iof;

class DtoToJsonTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Prepare object to test
     *
     * @return ToJsonExample
     */
    public function prepareObject()
    {
        $example       = new ToJsonExample();
        $example->four = new ToJsonObjSerialExample();
        $example->five = new ToJsonStrSerialExample();

        return [[$example]];
    }

    /**
     * @test
     * @dataProvider prepareObject
     *
     * @param ToJsonExample $object
     */
    public function make_sure_the_object_is_serializable(ToJsonExample $object)
    {
        $this->assertTrue(
            Iof::arrayable($object),
            'Make sure it contains toArray() method'
        );

        $this->assertTrue(
            Iof::jsonable($object),
            'Make sure it contains toJson() method'
        );
    }

    /**
     * @dataProvider prepareObject
     * @test
     *
     * @param ToJsonExample $object
     */
    public function make_sure_we_extract_json(ToJsonExample $object)
    {
        $this->assertJson($object->toJson());
    }

    /**
     * @dataProvider prepareObject
     * @test
     *
     * @param ToJsonExample $object
     */
    public function verify_serialization_with_default_flags(ToJsonExample $object)
    {
        $a = json_decode(
            $object->toJson(),
            TRUE
        );

        $this->assertArrayHasKey('one', $a, 'One exists?');
        $this->assertArrayHasKey('two', $a, 'Two exists?');
        $this->assertArrayNotHasKey('three', $a, 'Three exists?');
        $this->assertArrayHasKey('four', $a, 'Four exists?');
        $this->assertArrayHasKey('five', $a, 'Five exists?');
        $this->assertArrayHasKey('six', $a, 'Six exists?');
        $this->assertArrayHasKey('seven', $a, 'Sever exists?');

        $this->assertEquals('one', $a['one']);
        $this->assertEquals('two', $a['two']);
        $this->assertTrue(is_array($a['four']), 'Four should be an array');
        $this->assertEquals($a['four'], [41, 42, 43], 'Four Value');
        $this->assertTrue(is_array($a['five']), 'Five should be an array');
        $this->assertEquals($a['five'], ['five' => 5], 'Five Value');
        $this->assertNull($a['six']);
        $this->assertNull($a['seven']);
    }

    /**
     * @dataProvider prepareObject
     * @test
     *
     * @param ToJsonExample $object
     */
    public function verify_serialization_with_string_serialization(ToJsonExample $object)
    {
        $a = json_decode(
            $object->toJson(0, DtoToArrayFlags::SERIALIZE_STRING_PROVIDERS),
            TRUE
        );

        $this->assertArrayHasKey('one', $a, 'One exists?');
        $this->assertArrayHasKey('two', $a, 'Two exists?');
        $this->assertArrayNotHasKey('three', $a, 'Three exists?');
        $this->assertArrayHasKey('four', $a, 'Four exists?');
        $this->assertArrayHasKey('five', $a, 'Five exists?');
        $this->assertArrayHasKey('six', $a, 'Six exists?');
        $this->assertArrayHasKey('seven', $a, 'Sever exists?');

        $this->assertEquals('one', $a['one']);
        $this->assertEquals('two', $a['two']);
        $this->assertTrue(is_array($a['four']), 'Four should be an array');
        $this->assertEquals($a['four'], [41, 42, 43], 'Four Value');
        $this->assertFalse(is_array($a['five']), 'Five should be string');
        $this->assertEquals($a['five'], 'five', 'Five Value');
        $this->assertNull($a['six']);
        $this->assertNull($a['seven']);
    }

    /**
     * @dataProvider prepareObject
     * @test
     */
    public function verify_serialization_with_no_object_serialization(ToJsonExample $object)
    {
        $a = json_decode(
            $object->toJson(JSON_PRETTY_PRINT, DtoToArrayFlags::DONT_SERIALIZE_OBJECTS),
            TRUE
        );

        $this->assertArrayHasKey('one', $a, 'One exists?');
        $this->assertArrayHasKey('two', $a, 'Two exists?');
        $this->assertArrayNotHasKey('three', $a, 'Three exists?');
        $this->assertArrayHasKey('four', $a, 'Four exists?');
        $this->assertArrayHasKey('five', $a, 'Five exists?');
        $this->assertArrayHasKey('six', $a, 'Six exists?');
        $this->assertArrayHasKey('seven', $a, 'Sever exists?');

        $this->assertEquals('one', $a['one']);
        $this->assertEquals('two', $a['two']);
        $this->assertTrue(is_array($a['four']), 'Four should be an array');
        $this->assertEquals($a['four'], ['four' => 4], 'Four Value');
        $this->assertTrue(is_array($a['five']), 'Five should be an array');
        $this->assertEquals($a['five'], ['five' => 5], 'Five Value');
        $this->assertNull($a['six']);
        $this->assertNull($a['seven']);
    }

    /**
     * @dataProvider prepareObject
     * @test
     */
    public function verify_serialization_excluding_nulls_flag(ToJsonExample $object)
    {
        $a = json_decode(
            $object->toJson(
                0,
                DtoToArrayFlags::DONT_SERIALIZE_OBJECTS +
                DtoToArrayFlags::EXCLUDE_NULLS
            ),
            TRUE
        );

        $this->assertArrayHasKey('one', $a, 'One exists?');
        $this->assertArrayHasKey('two', $a, 'Two exists?');
        $this->assertArrayNotHasKey('three', $a, 'Three exists?');
        $this->assertArrayHasKey('four', $a, 'Four exists?');
        $this->assertArrayHasKey('five', $a, 'Five exists?');
        $this->assertArrayNotHasKey('six', $a, 'Six exists?');
        $this->assertArrayNotHasKey('seven', $a, 'Sever exists?');
    }
}

class ToJsonExample
{
    use \Fnp\Dto\Common\Traits\DtoToArray;
    use \Fnp\Dto\Common\Traits\DtoToJson;

    public    $one   = 'one';
    protected $two   = 'two';
    private   $three = 'three';
    public    $four;
    public    $five;
    public    $six;
    public    $seven;
}

class ToJsonObjSerialExample
{
    public $four = 4;

    public function toArray()
    {
        return [41, 42, 43];
    }
}

class ToJsonStrSerialExample
{
    public $five = 5;

    public function __toString()
    {
        return 'five';
    }
}

