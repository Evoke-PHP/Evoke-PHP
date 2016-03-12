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
            'defaults' => [
                'expected' => 'C1',
                'object'   => new Tokens('C1', 'P1'),
                'uri'      => 'P1/'
            ],
            'empty'    => [
                'expected' => 'ControllerName',
                'object'   => new Tokens('ControllerName', 'Prefix', '+'),
                'uri'      => 'Prefix'
            ],
            'one'      => [
                'expected' => 'ControllerName',
                'object'   => new Tokens('ControllerName', 'Prefix', '+'),
                'uri'      => 'Prefix+Part'
            ],
            'two'      => [
                'expected' => 'CTRL',
                'object'   => new Tokens('CTRL', 'PRE\FIX', '\\'),
                'uri'      => 'PRE\FIX\Part1\Part2'
            ]
        ];
    }

    public function providerMatch()
    {
        return [
            'empty_prefix_1'            => [
                'expected' => true,
                'object'   => new Tokens('DC', '', '\\'),
                'uri'      => 'Blah'
            ],
            'empty_prefix_2'            => [
                'expected' => true,
                'object'   => new Tokens('DC', '', '|'),
                'uri'      => 'a|b'
            ],
            'no_match_empty'            => [
                'expected' => false,
                'object'   => new Tokens('C', 'A', 'S'),
                'uri'      => ''
            ],
            'no_match_wrong_prefix'     => [
                'expected' => false,
                'object'   => new Tokens('A', 'B', 'C'),
                'uri'      => 'Z'
            ],
            'prefix_ends_in_token_char' => [
                'expected' => true,
                'object'   => new Tokens('DC', 'CON+', '+'),
                'uri'      => 'CON+'
            ],
            'prefix_match'              => [
                'expected' => true,
                'object'   => new Tokens('DC', 'match', '/'),
                'uri'      => 'match/token1/token2'
            ]
        ];
    }

    public function providerParams()
    {
        return [
            'one'        => [
                'expected' => ['Part'],
                'object'   => new Tokens('DC', 'Cont+', '+'),
                'uri'      => 'Cont+Part'
            ],
            'empty'  => [
                'expected' => [],
                'object'   => new Tokens('DC', 'Cont+', '+'),
                'uri'      => 'Cont+'
            ],
            'skip_empty'  => [
                'expected' => ['Part1AsOtherSkipped', 'Part2'],
                'object'   => new Tokens('DC', 'Controller', '+'),
                'uri'      => 'Controller++Part1AsOtherSkipped+++++++++Part2'
            ],
            'skip_many' => [
                'expected' => ['a', 'd', 'g'],
                'object'   => new Tokens('C', 'C|', '|*'),
                'uri'      => 'C|a|***|*|d||*||**|g'
            ],
            'skip_last' => [
                'expected' => ['Part1', 'Last'],
                'object'   => new Tokens('DC', 'Controller', '+'),
                'uri'      => 'Controller+Part1+Last+'
            ],
            'two'        => [
                'expected' => ['Part1', 'Part2'],
                'object'   => new Tokens('DC', 'Controller\\', '\\'),
                'uri'      => 'Controller\Part1\Part2'
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
