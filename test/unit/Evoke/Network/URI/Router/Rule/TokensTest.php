<?php
namespace Evoke_Test\Network\URI\Router\Rule;

use Evoke\Network\URI\Router\Rule\Tokens;
use PHPUnit_Framework_TestCase;

/**
 * @covers Evoke\Network\URI\Router\Rule\Tokens
 * @uses   Evoke\Network\URI\Router\Rule\Rule
 */
class TokensTest extends PHPUnit_Framework_TestCase
{
    /******************/
    /* Data Providers */
    /******************/

    public function providerController()
    {
        return [
            'Defaults' => [
                'Expected' => 'C1',
                'Object'   => new Tokens('C1', 'P1'),
                'URI'      => 'P1/'
            ],
            'Empty'    => [
                'Expected' => 'ControllerName',
                'Object'   => new Tokens('ControllerName', 'Prefix', '+'),
                'URI'      => 'Prefix'
            ],
            'One'      => [
                'Expected' => 'ControllerName',
                'Object'   => new Tokens('ControllerName', 'Prefix', '+'),
                'URI'      => 'Prefix+Part'
            ],
            'Two'      => [
                'Expected' => 'CTRL',
                'Object'   => new Tokens('CTRL', 'PRE\FIX', '\\'),
                'URI'      => 'PRE\FIX\Part1\Part2'
            ]
        ];
    }

    public function providerMatch()
    {
        return [
            'Empty_Prefix_1'            => [
                'Expected' => true,
                'Object'   => new Tokens('DC', '', '\\'),
                'URI'      => 'Blah'
            ],
            'Empty_Prefix_2'            => [
                'Expected' => true,
                'Object'   => new Tokens('DC', '', '|'),
                'URI'      => 'a|b'
            ],
            'No_Match_Empty'            => [
                'Expected' => false,
                'Object'   => new Tokens('C', 'A', 'S'),
                'URI'      => ''
            ],
            'No_Match_Wrong_Prefix'     => [
                'Expected' => false,
                'Object'   => new Tokens('A', 'B', 'C'),
                'URI'      => 'Z'
            ],
            'Prefix_Ends_In_Token_Char' => [
                'Expected' => true,
                'Object'   => new Tokens('DC', 'CON+', '+'),
                'URI'      => 'CON+'
            ],
            'Prefix_Match'              => [
                'Expected' => true,
                'Object'   => new Tokens('DC', 'match', '/'),
                'URI'      => 'match/token1/token2'
            ]
        ];
    }

    public function providerParams()
    {
        return [
            'One'        => [
                'Expected' => ['Part'],
                'Object'   => new Tokens('DC', 'Cont+', '+'),
                'URI'      => 'Cont+Part'
            ],
            'Empty'  => [
                'Expected' => [],
                'Object'   => new Tokens('DC', 'Cont+', '+'),
                'URI'      => 'Cont+'
            ],
            'Skip_Empty'  => [
                'Expected' => ['Part1AsOtherSkipped', 'Part2'],
                'Object'   => new Tokens('DC', 'Controller', '+'),
                'URI'      => 'Controller++Part1AsOtherSkipped+++++++++Part2'
            ],
            'Skip_Many' => [
                'Expected' => ['a', 'd', 'g'],
                'Object'   => new Tokens('C', 'C|', '|*'),
                'URI'      => 'C|a|***|*|d||*||**|g'
            ],
            'Skip_Last' => [
                'Expected' => ['Part1', 'Last'],
                'Object'   => new Tokens('DC', 'Controller', '+'),
                'URI'      => 'Controller+Part1+Last+'
            ],
            'Two'        => [
                'Expected' => ['Part1', 'Part2'],
                'Object'   => new Tokens('DC', 'Controller\\', '\\'),
                'URI'      => 'Controller\Part1\Part2'
            ],

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
        $obj = new Tokens('Controller', 'Prefix', 'TokenCharacters');
        $this->assertInstanceOf('Evoke\Network\URI\Router\Rule\Tokens', $obj);
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage need token characters as non-empty string.
     */
    public function testCreateInvalidSeparator()
    {
        $obj = new Tokens('Controller', 'Prefix', '');
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
// EOF
