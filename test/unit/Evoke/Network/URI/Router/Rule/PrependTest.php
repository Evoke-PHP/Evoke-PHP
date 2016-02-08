<?php
namespace Evoke_Test\Network\URI\Router\Rule;

use Evoke\Network\URI\Router\Rule\Prepend;
use PHPUnit_Framework_TestCase;

/**
 * @covers Evoke\Network\URI\Router\Rule\Prepend
 * @uses   Evoke\Network\URI\Router\Rule\Rule
 */
class PrependTest extends PHPUnit_Framework_TestCase
{
    /******************/
    /* Data Providers */
    /******************/

    public function providerGetController()
    {
        return [
            'Whitespace' => [
                'Prepend'  => ' ',
                'URI'      => 'any',
                'Expected' => ' any'
            ],
            'Empty'      => [
                'Prepend'  => 'Prep',
                'URI'      => '',
                'Expected' => 'Prep'
            ]
        ];
    }

    /*********/
    /* Tests */
    /*********/

    public function testCreate()
    {
        $obj = new Prepend('Prepend_String');
        $this->assertInstanceOf('Evoke\Network\URI\Router\Rule\Prepend', $obj);
    }

    /**
     * @dataProvider providerGetController
     */
    public function testGetController($prepend, $uri, $expected)
    {
        $obj = new Prepend($prepend);
        $obj->setURI($uri);
        $this->assertSame($expected, $obj->getController());
    }

    public function testIsMatch()
    {
        $obj = new Prepend('anyPrep');
        $obj->setURI('anyURI');
        $this->assertTrue($obj->isMatch());
    }
}
// EOF
