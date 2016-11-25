<?php
namespace Evoke_Test\Network\URI\Router\Rule;

use Evoke\Network\URI\Router\Rule\StrReplace;
use PHPUnit_Framework_TestCase;

/**
 * @covers Evoke\Network\URI\Router\Rule\StrReplace
 * @uses   Evoke\Network\URI\Router\Rule\Rule
 */
class StrReplaceTest extends PHPUnit_Framework_TestCase
{
    /******************/
    /* Data Providers */
    /******************/

    public function providerGetController()
    {
        return [
            'single'   => [
                'match'       => 'foo',
                'replacement' => 'bar',
                'uri'         => 'thisfoook',
                'expected'    => 'thisbarok'
            ],
            'multiple' => [
                'match'       => 'a',
                'replacement' => 'zow',
                'uri'         => 'arkansas',
                'expected'    => 'zowrkzownszows'
            ]
        ];
    }

    public function providerIsMatch()
    {
        return [
            'matches'   => [
                'match'       => 'match',
                'replacement' => 'DC',
                'uri'         => 'thismatches',
                'expected'    => true
            ],
            'unmatched' => [
                'match'       => 'NOT',
                'replacement' => 'DC',
                'uri'         => 'notInsensitiveToCase',
                'expected'    => false
            ]
        ];
    }

    /*********/
    /* Tests */
    /*********/

    public function testCreate()
    {
        $obj = new StrReplace('Match', 'Replacement');
        $this->assertInstanceOf('Evoke\Network\URI\Router\Rule\StrReplace', $obj);
    }

    /**
     * @dataProvider providerGetController
     */
    public function testGetController($match, $replacement, $uri, $expected)
    {
        $obj = new StrReplace($match, $replacement);
        $obj->setURI($uri);
        $this->assertSame($expected, $obj->getController());
    }

    /**
     * @dataProvider providerIsMatch
     */
    public function testIsMatch($match, $replacement, $uri, $expected)
    {
        $obj = new StrReplace($match, $replacement);
        $obj->setURI($uri);
        $this->assertSame($expected, $obj->isMatch());
    }
}
// EOF
