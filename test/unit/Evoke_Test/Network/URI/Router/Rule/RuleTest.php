<?php
namespace Evoke_Test\Network\URI\Router\Rule;

use Evoke\Network\URI\Router\Rule\Rule;
use PHPUnit_Framework_TestCase;

class TestRuleExtended extends Rule
{
    public function getController()
    {
        return $this->uri;
    }

    public function isMatch()
    {
        return true;
    }
}

/**
 * @covers Evoke\Network\URI\Router\Rule\Rule
 */
class RuleTest extends PHPUnit_Framework_TestCase
{
    public function providerIsAuthoritative()
    {
        return [
            'true'  => [true],
            'false' => [false]
        ];
    }

    public function providerSetURI()
    {
        return [
            'empty'       => [''],
            'long_string' => ['this/is/a/long/Uri']
        ];
    }

    public function providerSetURINonString()
    {
        return [
            'int'    => [123],
            'array'  => [['this is an array']],
            'object' => [new \StdClass]
        ];
    }

    /*********/
    /* Tests */
    /*********/

    public function testGetParams()
    {
        $obj = new TestRuleExtended(true);
        $this->assertSame([], $obj->getParams());
    }

    /**
     * @dataProvider providerIsAuthoritative
     */
    public function testIsAuthoritative($auth)
    {
        $obj = new TestRuleExtended($auth);
        $this->assertSame($auth, $obj->isAuthoritative());
    }

    /**
     * @dataProvider providerSetURI
     */
    public function testSetURI($uri)
    {
        $obj = new TestRuleExtended(true);
        $obj->setURI($uri);
        $this->assertSame($uri, $obj->getController());
    }

    /**
     * @dataProvider             providerSetURINonString
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage needs URI as string.
     */
    public function testSetURINonString($uriNonString)
    {
        $obj = new TestRuleExtended(true);
        $obj->setURI($uriNonString);
    }
}
// EOF
