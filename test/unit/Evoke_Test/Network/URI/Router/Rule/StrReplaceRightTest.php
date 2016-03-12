<?php
namespace Evoke_Test\Network\URI\Router\Rule;

use Evoke\Network\URI\Router\Rule\StrReplaceRight;
use PHPUnit_Framework_TestCase;

/**
 * @covers Evoke\Network\URI\Router\Rule\StrReplaceRight
 * @uses   Evoke\Network\URI\Router\Rule\Rule
 */
class StrReplaceRightTest extends PHPUnit_Framework_TestCase
{
    /******************/
    /* Data Providers */
    /******************/

    public function providerGetController()
    {
        return [
            'replace_empty' => [
                'uri'         => 'uriEndPart',
                'match'       => 'EndPart',
                'replacement' => '',
                'expected'    => 'uri'
            ],
            'change_end'    => [
                'uri'         => 'thisMatch',
                'match'       => 'Match',
                'replacement' => 'REP',
                'expected'    => 'thisREP'
            ]
        ];
    }

    public function providerMatch()
    {
        return [
            'match'     => [
                'uri'         => 'uriEndPart',
                'match'       => 'EndPart',
                'replacement' => 'DC',
                'expected'    => true
            ],
            'no_match'  => [
                'uri'         => 'uriNoMatch',
                'match'       => 'False',
                'replacement' => 'DC',
                'expected'    => false
            ],
            'not_right' => [
                'uri'         => 'uriMatchNotAtEnd',
                'match'       => 'Match',
                'replacement' => 'DC',
                'expected'    => false
            ]
        ];
    }

    /*********/
    /* Tests */
    /*********/

    /**
     * Construct an object.
     */
    public function testConstruct()
    {
        $obj = new StrReplaceRight('Match', 'Replace');
        $this->assertInstanceOf('Evoke\Network\URI\Router\Rule\StrReplaceRight', $obj);
    }

    /**
     * @dataProvider providerGetController
     */
    public function testGetController($uri, $match, $replacement, $expected)
    {
        $obj = new StrReplaceRight($match, $replacement);
        $obj->setURI($uri);

        $this->assertSame($expected, $obj->getController());
    }

    /**
     * @dataProvider providerMatch
     */
    public function testMatch($uri, $match, $replacement, $expected)
    {
        $obj = new StrReplaceRight($match, $replacement);
        $obj->setURI($uri);

        $this->assertSame($expected, $obj->isMatch());
    }
}
// EOF
