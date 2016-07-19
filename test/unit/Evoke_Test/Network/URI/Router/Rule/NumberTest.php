<?php
/**
 * NumberTest
 *
 * @package   Evoke_Test\Network\URI\Router\Rule
 */

namespace Evoke_Test\Network\URI\Router\Rule;


use Evoke\Network\URI\Router\Rule\Number;

/**
 * @covers Evoke\Network\URI\Router\Rule\Number
 * @uses   Evoke\Network\URI\Router\Rule\Rule
 */
class NumberTest extends \PHPUnit_Framework_TestCase
{
    public function providerController()
    {
        return [
            'Has_Number'        => [
                'expected' => 'Controller',
                'obj'      => new Number('Controller', 'Found', 'A/'),
                'uri'      => 'A/21'
            ]
        ];
    }

    public function providerMatch()
    {
        return [
            'Bad_Prefix' => [
                'expected' => false,
                'obj'      => new Number('DC', 'No', 'Bad/'),
                'uri'      => 'NotBad/'
            ],
            'Empty_All'  => [
                'expected' => false,
                'obj'      => new Number('DC', 'Key', ''),
                'uri'      => '',
            ],
            'Empty_Number' => [
                'expected' => false,
                'obj'      => new Number('DC', 'Key', 'Pre'),
                'uri'      => 'Pre',
            ],
            'Has_Number' => [
                'expected' => true,
                'obj'      => new Number('DC', 'Found', 'A/'),
                'uri'      => 'A/21'
            ],
            'No_Prefix' => [
                'expected' => true,
                'obj'      => new Number('DC', 'Found', ''),
                'uri'      => '34'
            ],
            'Not_Number' => [
                'expected' => false,
                'obj'      => new Number('DC', 'A', 'B'),
                'uri'      => 'BNotNumber'
            ]
        ];
    }

    public function providerParams()
    {
        return [
            'Has_Number'        => [
                'expected' => ['Found' => 21],
                'obj'      => new Number('DC', 'Found', 'A/'),
                'uri'      => 'A/21'
            ]
        ];
    }

    /*********/
    /* Tests */
    /*********/

    /**
     * @dataProvider providerController
     */
    public function testController($expected, $obj, $uri)
    {
        $obj->setURI($uri);
        $this->assertSame($expected, $obj->getController());
    }

    public function testCreate()
    {
        $obj = new Number('Controller', 'Key', 'Prefix');
        $this->assertInstanceOf('Evoke\Network\URI\Router\Rule\Number', $obj);
    }

    /**
     * @dataProvider providerMatch
     */
    public function testMatch($expected, $obj, $uri)
    {
        $obj->setURI($uri);
        $this->assertSame($expected, $obj->isMatch());
    }

    /**
     * @dataProvider providerParams
     */
    public function testParams($expected, $obj, $uri)
    {
        $obj->setURI($uri);
        $this->assertSame($expected, $obj->getParams());
    }
}
