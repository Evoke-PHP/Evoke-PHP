<?php
namespace Evoke_Test\Network\URI\Router\Rule;

use Evoke\Network\URI\Router\Rule\Trim;
use PHPUnit_Framework_TestCase;

/**
 * @covers Evoke\Network\URI\Router\Rule\Trim
 * @uses   Evoke\Network\URI\Router\Rule\Rule
 */
class TrimTest extends PHPUnit_Framework_TestCase
{
    public function providerGetController()
    {
        return [
            'whitespace' => [
                'characters' => " \t\n",
                'expected'   => 'Now trail',
                'uri'        => " \t\nNow trail\n \t"
            ],
            'abc'        => [
                'characters' => 'abc',
                'expected'   => 'def',
                'uri'        => 'accccbdefaccacbbb'
            ],
            'left_only'  => [
                'characters' => 'Z',
                'expected'   => '123',
                'uri'        => 'Z123'
            ],
            'right_only' => [
                'characters' => ' ',
                'expected'   => 'Input',
                'uri'        => 'Input      '
            ]
        ];
    }

    public function providerIsMatch()
    {
        return [
            'whitespace_unmatched'         =>
                [
                    'characters' => " \t\n",
                    'expected'   => false,
                    'uri'        => "NoWhitespace"
                ],
            'underscores_and_dots_matched' =>
                [
                    'characters' => '_.',
                    'expected'   => true,
                    'uri'        => '_abcde.'
                ],
            'first_character_match_Only'   =>
                [
                    'characters' => 'A',
                    'expected'   => true,
                    'uri'        => 'Aasfopwio'
                ],
            'last_match_only'              =>
                [
                    'characters' => 'Z',
                    'expected'   => true,
                    'uri'        => 'InputZ'
                ],
            'many_unmatched'               =>
                [
                    'characters' => 'abcdefghijklmnopABCDEFG',
                    'expected'   => false,
                    'uri'        => 'zZyY OK zwxq'
                ]
        ];
    }

    /*********/
    /* Tests */
    /*********/

    public function testCreate()
    {
        $obj = new Trim("chars");
        $this->assertInstanceOf('Evoke\Network\URI\Router\Rule\Trim', $obj);
    }

    /**
     * @dataProvider providerGetController
     */
    public function testGetController($characters, $expected, $uri)
    {
        $obj = new Trim($characters);
        $obj->setURI($uri);
        $this->assertSame($expected, $obj->getController());
    }

    /**
     * @dataProvider providerIsMatch
     */
    public function testIsMatch($characters, $expected, $uri)
    {
        $obj = new Trim($characters);
        $obj->setURI($uri);
        $this->assertSame($expected, $obj->isMatch());
    }
}
// EOF
