<?php
namespace Evoke_Test\Network\URI\Router\Rule;

use Evoke\Network\URI\Router\Rule\Split;
use PHPUnit_Framework_TestCase;

/**
 * @covers Evoke\Network\URI\Router\Rule\Split
 * @uses   Evoke\Network\URI\Router\Rule\Rule
 */
class SplitTest extends PHPUnit_Framework_TestCase
{
    /******************/
    /* Data Providers */
    /******************/

    public function providerController()
    {
        return [
            'one'       => [
                'ControllerName',
                new Split('ControllerName', ['One'], 'Prefix', '+'),
                'Prefix+Part'
            ],
            'one_empty' => [
                'CON',
                new Split('CON', ['One'], 'CON+', '+'),
                'CON+'
            ],
            'two'       => [
                'CTRL',
                new Split('CTRL', ['P1', 'P2'], 'PRE\FIX', '\\'),
                'PRE\FIX\Part1\Part2'
            ],
            'two_empty' => [
                'ThisIsIt',
                new Split('ThisIsIt', ['P1', 'P2'], 'C', '\\'),
                'C\\'
            ]
        ];
    }

    public function providerMatch()
    {
        return [
            'empty_prefix_1'      => [
                true,
                new Split('DC', ['One'], '', '\\'),
                'Blah'
            ],
            'empty_prefix_2'      => [
                true,
                new Split('DC', ['One', 'Two'], '', '|'),
                'a|b'
            ],
            'one_empty'           => [
                true,
                new Split('DC', ['One'], 'CON+', '+'),
                'CON+'
            ],
            'one_extra'           => [
                false,
                new Split('Cont', ['One'], 'Cont ', ' '),
                'Cont One Two'
            ],
            'one_match'           => [
                true,
                new Split('DC', ['One'], 'Cont+', '+'),
                'Cont+Part'
            ],
            'one_prefix_bad'      => [
                false,
                new Split('C', ['One'], 'A', 'S'),
                'C'
            ],
            'two_empty'           => [
                true,
                new Split('C', ['One', 'Two'], 'C**', '**'),
                'C**A**'
            ],
            'two_extra'           => [
                false,
                new Split('Cont', ['P1', 'P2'], 'Cont\\', '\\'),
                'Cont\One\Two\Three'
            ],
            'two_less'            => [
                false,
                new Split('Cont', ['P1', 'P2'], 'Cont\\', '\\'),
                'Cont\One'
            ],
            'two_match'           => [
                true,
                new Split('DC', ['P1', 'P2'], 'CTRL\\', '\\'),
                'CTRL\Part1\Part2'
            ],
            'two_prefix_bad'      => [
                false,
                new Split('C', ['One', 'Two'], 'A', 'S'),
                'XoneStwo'
            ],
            'many_empty'          => [
                true,
                new Split('C', ['A', 'B', 'C', 'D', 'E', 'F', 'G'], 'C|', '|'),
                'C|a|||d|||g'
            ],
            'prefix_without_sep'  => [
                true,
                new Split('Controller', ['A', 'B'], 'Prefix', 'S'),
                'PrefixaSb'
            ],
            'prefix_wo_sep_extra' => [
                false,
                new Split('DC', ['A', 'B'], 'PRE', 'S'),
                'PRESb_valueS'
            ]
        ];
    }

    public function providerParams()
    {
        return [
            'one'        => [
                ['One' => 'Part'],
                new Split('DC', ['One'], 'Cont+', '+'),
                'Cont+Part'
            ],
            'one_empty'  => [
                ['One' => ''],
                new Split('DC', ['One'], 'Cont+', '+'),
                'Cont+'
            ],
            'two'        => [
                ['P1' => 'Part1', 'P2' => 'Part2'],
                new Split('DC', ['P1', 'P2'], 'Controller\\', '\\'),
                'Controller\Part1\Part2'
            ],
            'two_empty'  => [
                ['P1' => 'Part1', 'P2' => ''],
                new Split('DC', ['P1', 'P2'], 'Controller\\', '\\'),
                'Controller\Part1\\'
            ],
            'many_empty' => [
                [
                    'A' => 'a',
                    'B' => '',
                    'C' => '',
                    'D' => 'd',
                    'E' => '',
                    'F' => '',
                    'G' => 'g'
                ],
                new Split('C', ['A', 'B', 'C', 'D', 'E', 'F', 'G'], 'C|', '|'),
                'C|a|||d|||g'
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
        $obj = new Split('Controller', ['Part_1', 'Part_2'], 'Prefix', 'Separator');
        $this->assertInstanceOf('Evoke\Network\URI\Router\Rule\Split', $obj);
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage need parts as non-empty array.
     */
    public function testCreateInvalidParts()
    {
        $obj = new Split('C', [], 'P', 'S');
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage need separator as non-empty string.
     */
    public function testCreateInvalidSeparator()
    {
        $obj = new Split('Controller', ['Parts'], 'Prefix', '');
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
