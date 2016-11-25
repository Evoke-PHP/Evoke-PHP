<?php
namespace Evoke_Test\View\HTML5;

use Evoke\View\HTML5\Backtrace;
use PHPUnit_Framework_TestCase;

/**
 * @covers Evoke\View\HTML5\Backtrace
 */
class BacktraceTest extends PHPUnit_Framework_TestCase
{
    /******************/
    /* Data Providers */
    /******************/

    public function providerGoodBacktraceElements()
    {
        return [
            'one_level' =>
                [
                    'backtrace' => [
                        [
                            'class'    => 'One',
                            'file'     => 'one.php',
                            'function' => 'oneUp',
                            'line'     => 111,
                            'type'     => 'one_type'
                        ]
                    ],
                    'expected'  =>
                        [
                            'ol',
                            ['class' => 'backtrace'],
                            [
                                [
                                    'li',
                                    [],
                                    [
                                        ['span', ['class' => 'file'], 'one.php'],
                                        ['span', ['class' => 'line'], '(111)'],
                                        ['span', ['class' => 'class'], 'One'],
                                        ['span', ['class' => 'type'], 'one_type'],
                                        ['span', ['class' => 'function'], 'oneUp']
                                    ]
                                ]
                            ]
                        ]
                ],
            'two_level' =>
                [
                    'backtrace' => [
                        [
                            'class'    => 'Funky',
                            'file'     => 'Funk.php',
                            'function' => 'funkItUp',
                            'line'     => 78,
                            'type'     => 'typed'
                        ],
                        [
                            'class'    => 'Boogie',
                            'file'     => 'Boog.php',
                            'function' => 'boogieItUp',
                            'type'     => 'btyped'
                        ]
                    ],
                    'expected'  =>
                        [
                            'ol',
                            ['class' => 'backtrace'],
                            [
                                [
                                    'li',
                                    [],
                                    [
                                        ['span', ['class' => 'file'], 'Funk.php'],
                                        ['span', ['class' => 'line'], '(78)'],
                                        ['span', ['class' => 'class'], 'Funky'],
                                        ['span', ['class' => 'type'], 'typed'],
                                        ['span', ['class' => 'function'], 'funkItUp']
                                    ]
                                ],
                                [
                                    'li',
                                    [],
                                    [
                                        ['span', ['class' => 'file'], 'Boog.php'],
                                        ['span', ['class' => 'class'], 'Boogie'],
                                        ['span', ['class' => 'type'], 'btyped'],
                                        ['span', ['class' => 'function'], 'boogieItUp']
                                    ]
                                ]
                            ]
                        ]
                ]
        ];
    }

    /*********/
    /* Tests */
    /*********/

    /**
     * @covers           Evoke\View\HTML5\Backtrace::get
     * @covers           Evoke\View\HTML5\Backtrace::set
     * @dataProvider     providerGoodBacktraceElements
     */
    public function testGoodBacktraceElements($backtrace, $expected)
    {
        $obj = new Backtrace;
        $obj->set($backtrace);
        $this->assertSame($expected, $obj->get());
    }

    /**
     * @covers                                   Evoke\View\HTML5\Backtrace::get
     * @expectedException        LogicException
     * @expectedExceptionMessage                 needs backtrace.
     */
    public function testGetEmtpy()
    {
        $obj = new Backtrace;
        $obj->get();
    }
}
// EOF
