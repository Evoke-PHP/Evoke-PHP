<?php
namespace Evoke_Test\Network\URI\Rule;

use Evoke\Network\URI\Rule\StrReplace;
use PHPUnit_Framework_TestCase;

/**
 * @covers Evoke\Network\URI\Rule\StrReplace
 * @uses   Evoke\Network\URI\Rule\Rule
 */
class StrReplaceTest extends PHPUnit_Framework_TestCase
{
    /******************/
    /* Data Providers */
    /******************/

    public function providerGetController()
    {
        return [
            'Single'   => [
                'Match'       => 'foo',
                'Replacement' => 'bar',
                'URI'         => 'thisfoook',
                'Expected'    => 'thisbarok'
            ],
            'Multiple' => [
                'Match'       => 'a',
                'Replacement' => 'zow',
                'URI'         => 'arkansas',
                'Expected'    => 'zowrkzownszows'
            ]
        ];
    }

    public function providerIsMatch()
    {
        return [
            'Matches'   => [
                'Match'       => 'match',
                'Replacement' => 'DC',
                'URI'         => 'thismatches',
                'Expected'    => true
            ],
            'Unmatched' => [
                'Match'       => 'NOT',
                'Replacement' => 'DC',
                'URI'         => 'notInsensitiveToCase',
                'Expected'    => false
            ]
        ];
    }

    /*********/
    /* Tests */
    /*********/

    public function testCreate()
    {
        $obj = new StrReplace('Match', 'Replacement');
        $this->assertInstanceOf('Evoke\Network\URI\Rule\StrReplace', $obj);
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
