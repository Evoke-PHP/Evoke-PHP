<?php
/**
 * AlwaysUCFirstTest
 *
 * @package   Evoke_Test\Network\URI\Router\Rule
 */

namespace Evoke_Test\Network\URI\Router\Rule;

use Evoke\Network\URI\Router\Rule\AlwaysUCFirst;

/**
 * @covers Evoke\Network\URI\Router\Rule\AlwaysUCFirst
 */
class AlwaysUCFirstTest extends \PHPUnit_Framework_TestCase
{
    /******************/
    /* Data Providers */
    /******************/

    public function providerConstruct()
    {
        return [
            'false' => [false],
            'true'  => [true]
        ];
    }

    public function providerGetController()
    {
        return [
            'needs_ucfirst'  => [
                'expected' => 'Needed_It',
                'obj'      => new AlwaysUcfirst,
                'uri'      => 'needed_It'
            ],
            'no_need_for_uc' => [
                'expected' => 'No_Need',
                'obj'      => new AlwaysUcfirst,
                'uri'      => 'No_Need'
            ]
        ];
    }

    public function providerIsMatch()
    {
        return [
            'Basic' => [
                'obj' => new AlwaysUcfirst(),
                'uri' => 'Any'
            ]
        ];
    }

    /*********/
    /* Tests */
    /*********/

    /**
     * @dataProvider providerConstruct
     */
    public function testConstruct($auth)
    {
        $this->assertInstanceOf('Evoke\Network\URI\Router\Rule\AlwaysUCFirst', new AlwaysUcfirst($auth));
    }

    /**
     * @dataProvider providerGetController
     */
    public function testGetController($expected, AlwaysUCFirst $obj, $uri)
    {
        $obj->setURI($uri);
        $this->assertEquals($expected, $obj->getController());
    }

    /**
     * @dataProvider providerIsMatch
     */
    public function testIsMatch($obj, $uri)
    {
        $obj->setURI($uri);
        $this->assertTrue($obj->isMatch());
    }
}
// EOF