<?php
namespace Evoke_Test\Network\HTTP\MediaType;

use Evoke\Network\HTTP\MediaType\Router,
    PHPUnit_Framework_TestCase;

/**
 * @covers Evoke\Network\HTTP\MediaType\Router
 */
class RouterTest extends PHPUnit_Framework_TestCase
{
    /******************/
    /* Data Providers */
    /******************/

    /*********/
    /* Tests */
    /*********/

    public function testSingleRule()
    {
        $rIndex = 0;
        $rule = $this->getMock('Evoke\Network\HTTP\MediaType\Rule\RuleIface');
        $rule
            ->expects($this->at($rIndex++))
            ->method('setMediaType')
            ->with(['InitialMediaType']);
        $rule
            ->expects($this->at($rIndex++))
            ->method('isMatch')
            ->with()
            ->will($this->returnValue(true));
        $rule
            ->expects($this->at($rIndex++))
            ->method('getOutputFormat')
            ->with()
            ->will($this->returnValue('OutputFormat'));

        $obj = new Router;
        $obj->addRule($rule);

        $this->assertSame('OutputFormat',
                          $obj->route([['InitialMediaType']]));
    }

    public function testTwoRules()
    {
        $rIndex = 0;
        $r1 = $this->getMock('Evoke\Network\HTTP\MediaType\Rule\RuleIface');

        // Check first media type.
        $r1
            ->expects($this->at($rIndex++))
            ->method('setMediaType')
            ->with(['FirstMediaType']);
        $r1
            ->expects($this->at($rIndex++))
            ->method('isMatch')
            ->with()
            ->will($this->returnValue(false));

        // Check second media type.
        $r1
            ->expects($this->at($rIndex++))
            ->method('setMediaType')
            ->with(['SecondMediaType']);
        $r1
            ->expects($this->at($rIndex++))
            ->method('isMatch')
            ->with()
            ->will($this->returnValue(false));
        $r1
            ->expects($this->never())
            ->method('getOutputFormat');

        $rIndex = 0;
        $r2 = $this->getMock('Evoke\Network\HTTP\MediaType\Rule\RuleIface');

        // Check first media type.
        $r2
            ->expects($this->at($rIndex++))
            ->method('setMediaType')
            ->with(['FirstMediaType']);
        $r2
            ->expects($this->at($rIndex++))
            ->method('isMatch')
            ->with()
            ->will($this->returnValue(false));

        // Check second media type.
        $r2
            ->expects($this->at($rIndex++))
            ->method('setMediaType')
            ->with(['SecondMediaType']);
        $r2
            ->expects($this->at($rIndex++))
            ->method('isMatch')
            ->with()
            ->will($this->returnValue(true));
        $r2
            ->expects($this->at($rIndex++))
            ->method('getOutputFormat')
            ->with()
            ->will($this->returnValue('TwoFormat'));

        $obj = new Router;
        $obj->addRule($r1);
        $obj->addRule($r2);

        $this->assertSame(
            'TwoFormat',
            $obj->route([['FirstMediaType'], ['SecondMediaType']]));
    }

    /**
     * @expectedException        OutOfBoundsException
     * @expectedExceptionMessage no output formats match
     */
    public function testNoOutputFormatsMatch()
    {
        $obj = new Router;
        $obj->route([['MediaType']]);
    }
}
// EOF