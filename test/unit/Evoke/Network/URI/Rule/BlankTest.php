<?php
namespace Evoke_Test\Network\URI\Rule;

use Evoke\Network\URI\Rule\Blank;
use PHPUnit_Framework_TestCase;

/**
 * @covers Evoke\Network\URI\Rule\Blank
 * @uses   Evoke\Network\URI\Rule\Rule
 */
class BlankTest extends PHPUnit_Framework_TestCase
{
    /******************/
    /* Data Providers */
    /******************/

    public function providerIsMatch()
    {
        return [
            'Is_Blank'  => [
                'Replacement' => "Replace",
                'Expected'    => true,
                'URI'         => ""
            ],
            'Non_Blank' => [
                'Replacement' => 'abc',
                'Expected'    => false,
                'URI'         => 'nonBlank'
            ]
        ];
    }

    /*********/
    /* Tests */
    /*********/

    public function testCreate()
    {
        $obj = new Blank("ReplaceText");
        $this->assertInstanceOf('Evoke\Network\URI\Rule\Blank', $obj);
    }

    public function testGetController()
    {
        $obj = new Blank('Replacement');
        $obj->setURI('');
        $this->assertSame('Replacement', $obj->getController());
    }

    /**
     * @dataProvider providerIsMatch
     */
    public function testIsMatch($replacement, $expected, $uri)
    {
        $obj = new Blank($replacement);
        $obj->setURI($uri);
        $this->assertSame($expected, $obj->isMatch());
    }
}
// EOF
