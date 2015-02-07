<?php
namespace Evoke_Test\Network\URI\Rule;

use Evoke\Network\URI\Rule\RegexNamed;
use PHPUnit_Framework_TestCase;

/**
 * @covers Evoke\Network\URI\Rule\RegexNamed
 * @uses   Evoke\Network\URI\Rule\Rule
 */
class RegexNamedTest extends PHPUnit_Framework_TestCase
{
    /*********/
    /* Tests */
    /*********/

    public function testCreate()
    {
        $object = new RegexNamed('/match/', 'replacement');
        $this->assertInstanceOf('Evoke\Network\URI\Rule\RegexNamed', $object);
    }

    public function testGetController()
    {
        $object = new RegexNamed('/match/', 'replacement');
        $object->setURI('uri/matches/ok');
        $this->assertSame('uri/replacementes/ok', $object->getController());
    }

    public function testGetParams()
    {
        $object = new RegexNamed('/m(...)a(?<Named>N.*)fin/', 'rep');
        $object->setURI('m123aNamedMatchfin');
        $this->assertSame(['Named' => 'NamedMatch'], $object->getParams());
    }

    public function testIsMatchFalse()
    {
        $object = new RegexNamed('/m(...)a(?<Named>N.*)fin/', 'rep');
        $object->setURI('maNamedNoMatchFun');
        $this->assertFalse($object->isMatch());
    }

    public function testIsMatchTrue()
    {
        $object = new RegexNamed('/m\d[A-G]/', 'rep');
        $object->setURI('m1G');
        $this->assertTrue($object->isMatch());
    }
}
// EOF
