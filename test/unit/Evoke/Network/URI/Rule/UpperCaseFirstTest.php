<?php
namespace Evoke_Test\Network\URI\Rule;

use Evoke\Network\URI\Rule\UpperCaseFirst;
use PHPUnit_Framework_TestCase;

/**
 * @covers Evoke\Network\URI\Rule\UpperCaseFirst
 * @uses   Evoke\Network\URI\Rule\Rule
 */
class UpperCaseFirstTest extends PHPUnit_Framework_TestCase
{
    /*********/
    /* Tests */
    /*********/

    public function testCreate()
    {
        $obj = new UpperCaseFirst(['_']);
        $this->assertInstanceOf('Evoke\Network\URI\Rule\UpperCaseFirst', $obj);
    }

    public function testGetController()
    {
        $obj = new UpperCaseFirst(['_', ' ']);
        $obj->setURI('first LETTER_uppercased');
        $this->assertSame('First LETTER_Uppercased', $obj->getController());
    }

    public function testIsMatchFalse()
    {
        $obj = new UpperCaseFirst(['/']);
        $obj->setURI('thisDontMatch');
        $this->assertFalse($obj->isMatch());
    }

    public function testIsMatchTrue()
    {
        $obj = new UpperCaseFirst(['/']);
        $obj->setURI('this/matches');
        $this->assertTrue($obj->isMatch());
    }
}
// EOF
