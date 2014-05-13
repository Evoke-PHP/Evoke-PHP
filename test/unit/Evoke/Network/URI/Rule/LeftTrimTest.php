<?php
namespace Evoke_Test\Network\URI\Rule;

use Evoke\Network\URI\Rule\LeftTrim,
    PHPUnit_Framework_TestCase;

/**
 * @covers Evoke\Network\URI\Rule\LeftTrim
 * @uses   Evoke\Network\URI\Rule\Rule
 */
class LeftTrimTest extends PHPUnit_Framework_TestCase
{
    /******************/
    /* Data Providers */
    /******************/

    public function providerGetController()
    {
        return ['Whitespace' => ['Characters' => " \t\n",
                                 'Expected'   => "Now trail\n \t",
                                 'URI'        => " \t\nNow trail\n \t"],
                'abc'        => ['Characters' => 'abc',
                                 'Expected'   => 'defaccacbbb',
                                 'URI'        => 'accccbdefaccacbbb'],
                'Left_Only'  => ['Characters' => 'Z',
                                 'Expected'   => '123Z',
                                 'URI'        => 'Z123Z'],
                'Right_DC'   => ['Characters' => ' ',
                                 'Expected'   => 'Input      ',
                                 'URI'        => 'Input      '],
            ];
    }

    public function providerIsMatch()
    {
        return ['Whitespace_Unmatched'         =>
                ['Characters' => " \t\n",
                 'Expected'   => false,
                 'URI'        => "NoWhitespace"],
                'Underscores_And_Dots_Matched' =>
                ['Characters' => '_.',
                 'Expected'   => true,
                 'URI'        => '_.abcde'],
                'First_Character_Match_Only'   =>
                ['Characters' => 'A',
                 'Expected'   => true,
                 'URI'        => 'Aasfopwio'],
                'Many_Unmatched'               =>
                ['Characters' => 'abcdefghijklmnopABCDEFG',
                 'Expected'   => false,
                 'URI'        => 'zZyY OK zwxq'],
                'Empty_NonMatch'               =>
                ['Characters' => 'DC',
                 'Expected'   => false,
                 'URI'        => '']
            ];
    }

    /*********/
    /* Tests */
    /*********/

    public function testCreate()
    {
        $obj = new LeftTrim("chars");
        $this->assertInstanceOf('Evoke\Network\URI\Rule\LeftTrim', $obj);
    }

    /**
     * @dataProvider providerGetController
     */
    public function testGetController($characters, $expected, $uri)
    {
        $obj = new LeftTrim($characters);
        $obj->setURI($uri);
        $this->assertSame($expected, $obj->getController());
    }

    /**
     * @dataProvider providerIsMatch
     */
    public function testIsMatch($characters, $expected, $uri)
    {
        $obj = new LeftTrim($characters);
        $obj->setURI($uri);
        $this->assertSame($expected, $obj->isMatch());
    }
}
// EOF