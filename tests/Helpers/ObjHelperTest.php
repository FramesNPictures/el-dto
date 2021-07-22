<?php

use Fnp\ElHelper\Obj;
use Fnp\ElHelper\Str;

class ObjHelperTest extends \PHPUnit_Framework_TestCase
{
    public function provideMethodNamesTestData()
    {
        return [
            'All Lower Case'              => [
                'get', 'name', NULL, 'getName',
            ],
            'All Lower Case with Suffix'  => [
                'get', 'name', 'attribute', 'getNameAttribute',
            ],
            'All Lower without Prefix'    => [
                '', 'name', 'attribute', 'nameAttribute',
            ],
            'Snake Case Name'             => [
                'fill', 'post_code', NULL, 'fillPostCode',
            ],
            'Snake Case Name with Suffix' => [
                'fill', 'post_code', 'Value', 'fillPostCodeValue',
            ],
            'Snake Case without Prefix'   => [
                NULL, 'post_code', 'Value', 'postCodeValue',
            ],
            'Upper Case Name'             => [
                'fill', 'POST_CODE', NULL, 'fillPostCode',
            ],
            'Upper Case with Suffix'      => [
                'fill', 'POST_CODE', 'attributeValue', 'fillPostCodeAttributeValue',
            ],
            'Upper Case without Prefix'   => [
                '', 'POST_CODE', 'value', 'postCodeValue',
            ],
            'Upper Case with Prefix'      => [
                'get', 'POST_CODE', 'value', 'getPostCodeValue',
            ],
            'Upper Case one Word'         => [
                'get', 'VALUES', NULL, 'getValues',
            ],
            'Just Name Upper'             => [
                NULL, 'VALUES', NULL, 'values',
            ],
            'Just Name Lower'             => [
                NULL, 'values', NULL, 'values',
            ],
            'Just Name Snake'             => [
                NULL, 'post_code', NULL, 'postCode',
            ],
            'Just Name Camel'             => [
                NULL, 'postCode', NULL, 'postCode',
            ],
            'Dot Separated'               => [
                'set', 'dot.separated.value', NULL, 'setDotSeparatedValue',
            ],
            'Dash Separated'              => [
                '', 'dash-separated-value', 'Attribute', 'dashSeparatedValueAttribute',
            ],
        ];
    }

    public function provideCamelTestData()
    {
        return [
            ['username', 'username'],
            ['user_name', 'userName'],
            ['USER_NAME', 'userName'],
            ['USER-NAME', 'userName'],
            ['userName', 'userName'],
            ['userName2', 'userName2'],
            ['address_line_1', 'addressLine1'],
            ['address_line_2', 'addressLine2'],
            ['addressLine_1', 'addressLine1'],
            ['address_line2', 'addressLine2'],
            ['help_line_911', 'helpLine911'],
            ['help_line911', 'helpLine911'],
            ['TECHNOLOGY_ALL', 'technologyAll'],
            ['TECHNOLOGY', 'technology'],
        ];
    }

    public function provideSnakeTestData()
    {
        return [
            ['username', 'username'],
            ['user_name', 'user_name'],
            ['USER_NAME', 'user_name'],
            ['userName', 'user_name'],
            ['address_line_1', 'address_line_1'],
            ['address_line_2', 'address_line_2'],
            ['addressLine_1', 'address_line_1'],
            ['address_line2', 'address_line_2'],
            ['helpLine911', 'help_line_911'],
            ['HELP_LINE911', 'help_line_911'],
            ['helpLine911', 'help_line_911'],
            ['TECHNOLOGY_ALL', 'technology_all'],
            ['TECHNOLOGY', 'technology'],
        ];
    }

    public function provideAllCapsTestData()
    {
        return [
            ['THISisATEST', FALSE],
            ['THISISATEST', TRUE],
            ['this_is_a_TEST', FALSE],
            ['THIS_IS_A-TEST', TRUE],
            ['THIS.IS!A!$TEST', TRUE],
        ];
    }

    /**
     * @test
     *
     * @param $prefix
     * @param $name
     * @param $suffix
     * @param $methodName
     *
     * @dataProvider provideMethodNamesTestData
     */
    public function testMethodNames($prefix, $name, $suffix, $methodName)
    {
        $this->assertEquals($methodName, Obj::methodName($prefix, $name, $suffix));
    }

    /**
     * @test
     *
     * @dataProvider provideAllCapsTestData
     *
     * @param $value
     * @param $isAllCaps
     */
    public function testAllCaps($value, $isAllCaps)
    {
        $this->assertEquals($isAllCaps, Str::isAllCaps($value));
    }

    /**
     * @test
     *
     * @dataProvider provideCamelTestData
     *
     * @param $input
     * @param $output
     */
    public function testCamel($input, $output)
    {
        $this->assertEquals($output, Str::camel($input));
    }

    /**
     * @test
     *
     * @dataProvider provideSnakeTestData
     *
     * @param $input
     * @param $output
     */
    public function testSnake($input, $output)
    {
        $this->assertEquals($output, Str::snake($input));
    }
}