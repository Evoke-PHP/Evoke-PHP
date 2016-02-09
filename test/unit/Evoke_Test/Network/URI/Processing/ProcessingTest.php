<?php
/**
 * ProcessingTest
 *
 * @package   Evoke_Test\Network\URI\Processing
 */

namespace Evoke_Test\Network\URI\Processing;

use DomainException;
use Evoke\Network\URI\Processing\Processing;
use PHPUnit_Framework_TestCase;

/**
 * ProcessingTest
 *
 * @covers Evoke\Network\URI\Processing\Processing
 */
class ProcessingTest extends PHPUnit_Framework_TestCase
{
    /******************/
    /* Data Providers */
    /******************/

    public function providerDefaultBehaviourAllowsZeroOneOrMoreMatches()
    {
        $matchedRule = $this->getMock('Evoke\Network\URI\Processing\Rule\RuleIface');
        $matchedRule
            ->expects($this->any())
            ->method('isMatch')
            ->will($this->returnValue(true));

        $unmatchedRule  = $this->getMock('Evoke\Network\URI\Processing\Rule\RuleIface');
        $unmatchedRule
            ->expects($this->any())
            ->method('isMatch')
            ->will($this->returnValue(false));

        return [
            'More' => [
                'Data'  => ['DC1' => 'A', 'DC2' => 'B'],
                'Rules' => [$matchedRule, $matchedRule, $unmatchedRule, $matchedRule]
            ]
        ];
    }

    /*********/
    /* Tests */
    /*********/

    /**
     * @dataProvider providerDefaultBehaviourAllowsZeroOneOrMoreMatches
     */
    public function testDefaultBehaviourAllowsZeroOneOrMoreMatches(Array $data, Array $rules)
    {
        $obj = new Processing;

        foreach ($rules as $rule) {
            $obj->addRule($rule);
        }

        $obj->setData($data);
        $obj->process();

        // An exception would be thrown if the default behaviour did not allow zero one or more matches.
        $this->assertTrue(true);
    }


    /**
     * @expectedException        DomainException
     * @expectedExceptionMessage Match required while processing:
     */
    public function testMatchRequiredButNoRulesMatch()
    {
        $obj = new Processing;
        $obj->setMatchRequired();
        $obj->process();
    }

    public function testOnlyMatchedRulesAreExecutedOnce()
    {
        $matchedRule1 = $this->getMock('Evoke\Network\URI\Processing\Rule\RuleIface');
        $matchedRule1
            ->expects($this->any())
            ->method('isMatch')
            ->will($this->returnValue(true));
        $matchedRule1
            ->expects($this->once())
            ->method('execute');

        $matchedRule2 = $this->getMock('Evoke\Network\URI\Processing\Rule\RuleIface');
        $matchedRule2
            ->expects($this->any())
            ->method('isMatch')
            ->will($this->returnValue(true));
        $matchedRule2
            ->expects($this->once())
            ->method('execute');

        $unmatchedRule  = $this->getMock('Evoke\Network\URI\Processing\Rule\RuleIface');
        $unmatchedRule
            ->expects($this->any())
            ->method('isMatch')
            ->will($this->returnValue(false));
        $unmatchedRule
            ->expects($this->never())
            ->method('execute');

        $obj = new Processing();
        $obj->addRule($matchedRule1);
        $obj->addRule($unmatchedRule);
        $obj->addRule($matchedRule2);
        $obj->addRule($unmatchedRule);
        $obj->process();
    }

    /**
     * @expectedException        DomainException
     * @expectedExceptionMessage Unique match required while processing:
     */
    public function testUniqueMatchRequiredButMultipleExist()
    {
        $obj = new Processing;
        $obj->setUniqueMatchRequired();

        $matchedRule = $this->getMock('Evoke\Network\URI\Processing\Rule\RuleIface');
        $matchedRule
            ->expects($this->any())
            ->method('isMatch')
            ->will($this->returnValue(true));

        $obj->addRule($matchedRule);
        $obj->addRule($matchedRule);
        $obj->process();
    }
}
