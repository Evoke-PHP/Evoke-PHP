<?php
namespace Evoke_Test\Network\URI\Router\Rule;

use Evoke\Network\URI\Router\Rule\Rule;
use PHPUnit_Framework_TestCase;

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
}
// EOF
