<?php
namespace Evoke_Test\Network\URI\Router;

use Evoke\Network\URI\Router\Router;
use PHPUnit_Framework_TestCase;

class RouterTest extends PHPUnit_Framework_TestCase
{
    /*********/
    /* Tests */
    /*********/

    /**
     * @covers Evoke\Network\URI\Router\Router::addRule
     * @covers Evoke\Network\URI\Router\Router::route
     */
    public function testSingleRule()
    {
        $ruleIndex = 0;
        $rule      = $this->getMock('Evoke\Network\URI\Router\Rule\RuleIface');
        $rule
            ->expects($this->at($ruleIndex++))
            ->method('setURI')
            ->with('oldURI');
        $rule
            ->expects($this->at($ruleIndex++))
            ->method('isMatch')
            ->with()
            ->will($this->returnValue(true));
        $rule
            ->expects($this->at($ruleIndex++))
            ->method('getController')
            ->with()
            ->will($this->returnValue('newController'));
        $rule
            ->expects($this->at($ruleIndex++))
            ->method('getParams')
            ->with()
            ->will($this->returnValue(['params' => 'Found']));
        $rule
            ->expects($this->at($ruleIndex++))
            ->method('isAuthoritative')
            ->with()
            ->will($this->returnValue(false));

        $obj = new Router;
        $obj->addRule($rule);

        $this->assertSame(
            [
                'controller' => 'newController',
                'params'     => ['params' => 'Found']
            ],
            $obj->route('oldURI')
        );
    }

    /**
     * @covers Evoke\Network\URI\Router\Router::addRule
     * @covers Evoke\Network\URI\Router\Router::route
     */
    public function testThreeRulesSecondAuthoritative()
    {
        $rIndex = 0;
        $r1     = $this->getMock('Evoke\Network\URI\Router\Rule\RuleIface');
        $r1
            ->expects($this->at($rIndex++))
            ->method('setURI')
            ->with('oldURI');
        $r1
            ->expects($this->at($rIndex++))
            ->method('isMatch')
            ->with()
            ->will($this->returnValue(true));
        $r1
            ->expects($this->at($rIndex++))
            ->method('getController')
            ->with()
            ->will($this->returnValue('newController'));
        $r1
            ->expects($this->at($rIndex++))
            ->method('getParams')
            ->with()
            ->will($this->returnValue(['params' => 'Found']));
        $r1
            ->expects($this->at($rIndex++))
            ->method('isAuthoritative')
            ->with()
            ->will($this->returnValue(false));

        $rIndex = 0;
        $r2     = $this->getMock('Evoke\Network\URI\Router\Rule\RuleIface');
        $r2
            ->expects($this->at($rIndex++))
            ->method('setURI')
            ->with('newController');
        $r2
            ->expects($this->at($rIndex++))
            ->method('isMatch')
            ->with()
            ->will($this->returnValue(true));
        $r2
            ->expects($this->at($rIndex++))
            ->method('getController')
            ->with()
            ->will($this->returnValue('refinedURI'));
        $r2
            ->expects($this->at($rIndex++))
            ->method('getParams')
            ->with()
            ->will($this->returnValue(['more' => 'Params']));
        $r2
            ->expects($this->at($rIndex++))
            ->method('isAuthoritative')
            ->with()
            ->will($this->returnValue(true));

        $r3 = $this->getMock('Evoke\Network\URI\Router\Rule\RuleIface');
        $r3->expects($this->never())->method('setURI');
        $r3->expects($this->never())->method('isMatch');
        $r3->expects($this->never())->method('getController');
        $r3->expects($this->never())->method('getParams');
        $r3->expects($this->never())->method('isAuthoritative');

        $obj = new Router;
        $obj->addRule($r1);
        $obj->addRule($r2);
        $obj->addRule($r3);

        $this->assertSame(
            [
                'controller' => 'refinedURI',
                'params'     => [
                    'params' => 'Found',
                    'more'   => 'Params'
                ]
            ],
            $obj->route('oldURI')
        );
    }
}
// EOF
