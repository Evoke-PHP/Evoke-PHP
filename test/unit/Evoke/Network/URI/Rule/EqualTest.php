<?php
namespace Evoke_Test\Network\URI\Rule;

use Evoke\Network\URI\Rule\Equal,
    PHPUnit_Framework_TestCase;

/**
 * @covers Evoke\Network\URI\Rule\Equal
 * @uses   Evoke\Network\URI\Rule\Rule
 */
class EqualTest extends PHPUnit_Framework_TestCase
{
    /******************/
    /* Data Providers */
    /******************/

    public function providerMatch()
    {
        return [
            'Is_Equal' => [true,
                           new Equal('C', 'M'),
                           'M']
            ];
    }

    /*********/
    /* Tests */
    /*********/

    public function testCreate()
    {
        $obj = new Equal('controller', 'match', ['Params' => 1], true);
        $this->assertInstanceOf('Evoke\Network\URI\Rule\Equal', $obj);
    }

    public function testGetController()
    {
        $obj = new Equal('controller', 'match', ['Params' => 1], true);
        $this->assertSame('controller', $obj->getController());
    }

    public function testGetParams()
    {
        $obj = new Equal('controller', 'match', ['Params' => 1], true);
        $this->assertSame(['Params' => 1], $obj->getParams());
    }
    
    /**
     * @dataProvider providerMatch
     */
    public function testMatch($expected, $obj, $uri)
    {
        $obj->setURI($uri);
        $this->assertSame($expected, $obj->isMatch($uri));
    }
}
// EOF