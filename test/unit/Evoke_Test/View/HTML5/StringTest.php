<?php
namespace Evoke_Test\View\HTML5;

use Evoke\View\HTML5\String;
use PHPUnit_Framework_TestCase;

/**
 * @covers Evoke\View\HTML5\String
 */
class StringTest extends PHPUnit_Framework_TestCase
{
    /******************/
    /* Data Providers */
    /******************/

    public function providerString()
    {
        return [
            'commented'      => [
                'expected' => ['One', ['div', [], 'Two'], 'Three'],
                'string'   => 'One<!-- C1 --><div>Two</div><!-- C2 -->Three'
            ],
            'multi_nested'   => [
                'expected' =>
                    [
                        [
                            'div',
                            ['class' => 'First'],
                            [
                                ['div', [], 'A'],
                                ['div', ['class' => 'Number'], '1']
                            ]
                        ],
                        [
                            'div',
                            ['class' => 'Mid'],
                            [
                                ['div', [], 'M'],
                                ['div', ['class' => 'Number'], '5']
                            ]
                        ],
                        [
                            'div',
                            ['class' => 'Last'],
                            [
                                ['div', [], 'Z'],
                                ['div', ['class' => 'Number'], '9']
                            ]
                        ]
                    ],
                'string'   =>
                    '<div class="First">' .
                    '<div>A</div><div class="Number">1</div></div>' .
                    '<div class="Mid">' .
                    '<div>M</div><div class="Number">5</div></div>' .
                    '<div class="Last">' .
                    '<div>Z</div><div class="Number">9</div></div>'
            ],
            'single_nested'  => [
                'expected' => [
                    [
                        'div',
                        [],
                        [
                            ['span', [], 'SP THIS'],
                            [
                                'div',
                                ['class' => 'Other'],
                                [['div', [], 'Alt']]
                            ]
                        ]
                    ]
                ],
                'string'   => '<div><span>SP THIS</span><div class="Other">' .
                    '<div>Alt</div></div></div>'
            ],
            'single_string'  => [
                'expected' => 'str',
                'string'   => 'str'
            ],
            'single_cdata'   => [
                'expected' => 'this <div> can appear > CDATA &! all.',
                'string'   =>
                    '<![CDATA[this <div> can appear > CDATA &! all.]]>'
            ],
            'single_element' => [
                'expected' => [['span', [], 'SP THIS']],
                'string'   => '<span>SP THIS</span>'
            ],
        ];
    }

    /*********/
    /* Tests */
    /*********/

    /**
     * @dataProvider providerString
     */
    public function testString($expected, $string)
    {
        $obj = new String;
        $obj->setHTML5($string);

        $this->assertSame($expected, $obj->get());
    }
}
// EOF
