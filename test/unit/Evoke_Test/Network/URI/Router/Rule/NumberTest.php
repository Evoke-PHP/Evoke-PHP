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
            'Is_Number_Prefix_Only' => [
                'expected' => 'Controller',
                'obj'      => new Number('Controller', 'Found', 'A/'),
                'uri'      => 'A/21'
            ],
            'Is_Number_With_Suffix' => [
                'expected' => 'Controller',
                'obj'      => new Number('Controller', 'Found', 'A/', '/SUF'),
                'uri'      => 'A/21/SUF'
            ]
        ];
    }

    public function providerMatch()
    {
        return [
            'Bad_Prefix'           => [
                'expected' => false,
                'obj'      => new Number('DC', 'No', 'Bad/'),
                'uri'      => 'NotBad/'
            ],
            'Bad_Sandwich_Filling' => [
                'expected' => false,
                'obj'      => new Number('Controller', 'Key', 'Top_Slice/', '/Bottom_Slice'),
                'uri'      => 'Top_Slice/BadFilling/Bottom_Slice'
            ],
            'Bad_Suffix'           => [
                'expected' => false,
                'obj'      => new Number('DCController', 'DCKey', 'Good/', '/Bad'),
                'uri'      => 'Good/1/NotBad'
            ],
            'Empty_All'            => [
                'expected' => false,
                'obj'      => new Number('DC', 'Key', '', ''),
                'uri'      => '',
            ],
            'Empty_All_Default'    => [
                'expected' => false,
                'obj'      => new Number('DC', 'Key', ''),
                'uri'      => '',
            ],
            'Empty_Number'         => [
                'expected' => false,
                'obj'      => new Number('DC', 'Key', 'Pre'),
                'uri'      => 'Pre',
            ],
            'Has_Number'           => [
                'expected' => true,
                'obj'      => new Number('DC', 'Found', 'A/'),
                'uri'      => 'A/21'
            ],
            'No_Prefix'            => [
                'expected' => true,
                'obj'      => new Number('DC', 'Found', ''),
                'uri'      => '34'
            ],
            'No_Prefix_Has_Suffix' => [
                'expected' => true,
                'obj'      => new Number('DC', 'Found', '', 'SUF'),
                'uri'      => '34SUF'
            ],
            'Not_Number'           => [
                'expected' => false,
                'obj'      => new Number('DC', 'A', 'B'),
                'uri'      => 'BNotNumber'
            ],
            'SUF_Not_Number'       => [
                'expected' => false,
                'obj'      => new Number('DC', 'A', '', 'B'),
                'uri'      => 'NotNumberB'
            ]
        ];
    }

    public function providerParams()
    {
        return [
            'Both'   => [
                'expected' => ['Found' => 21],
                'obj'      => new Number('DC', 'Found', 'A/', '/B'),
                'uri'      => 'A/21/B'
            ],
            'Empty'  => [
                'expected' => ['Found' => 23],
                'obj'      => new Number('DC', 'Found', '', ''),
                'uri'      => '23'
            ],
            'Prefix' => [
                'expected' => ['Found' => 25],
                'obj'      => new Number('DC', 'Found', 'PRE/'),
                'uri'      => 'PRE/25'
            ],
            'Suffix' => [
                'expected' => ['Found' => 27],
                'obj'      => new Number('DC', 'Found', '', '/SUF'),
                'uri'      => '27/SUF'
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
