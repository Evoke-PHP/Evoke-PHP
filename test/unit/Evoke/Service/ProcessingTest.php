<?php
namespace Evoke_Test\Service;

use Evoke\Service\Processing;
use PHPUnit_Framework_TestCase;

class TestCallbacks
{
    protected $args;
    protected $argsStack = [];

    public function getArgs()
    {
        return $this->args;
    }

    public function getArgsStack()
    {
        return $this->argsStack;
    }

    public function setArgs()
    {
        $arguments         = func_get_args();
        $this->args        = $arguments;
        $this->argsStack[] = $arguments;
    }
}

/**
 * @covers Evoke\Service\Processing
 */
class ProcessingTest extends PHPUnit_Framework_TestCase
{
    /*********/
    /* Tests */
    /*********/

    public function testEmptyRequestWithCallback()
    {
        $obj = new Processing;
        $obj->setData([]);
        $testCallbacks = new TestCallbacks;
        $obj->addCallback('', [$testCallbacks, 'setArgs']);
        $obj->setMatchRequired();
        $obj->setUniqueMatchRequired();
        $obj->process();

        $this->assertSame([[]], $testCallbacks->getArgs());
    }

    public function testEmptyRequestWithoutCallback()
    {
        $obj = new Processing;
        $obj->setData([]);
        $obj->process();

        $this->assertTrue(true, 'Processing should do nothing.');
    }

    public function testCallsCallbackWithAllButRequestKey()
    {
        $obj           = new Processing;
        $testCallbacks = new TestCallbacks;
        $obj->addCallback('RQ', [$testCallbacks, 'setArgs']);
        $obj->setData(['Val' => 1, 'RQ' => 'Key', 'V2' => 2]);
        $obj->process();

        $this->assertSame([['Val' => 1, 'V2' => 2]], $testCallbacks->getArgs());
    }

    public function testCallsAllCallbacks()
    {
        $obj           = new Processing(true, true);
        $testCallbacks = new TestCallbacks;
        $obj->addCallback('RQ1', [$testCallbacks, 'setArgs']);
        $obj->addCallback('RQ2', [$testCallbacks, 'setArgs']);
        $obj->setMatchRequired();
        $obj->setUniqueMatchRequired(false);
        $obj->setData([
            'V1'  => 1,
            'RQ1' => 'K1',
            'RQ2' => 'K2',
            'V2'  => 2
        ]);
        $obj->process();

        $this->assertSame(
            [
                [['V1' => 1, 'RQ2' => 'K2', 'V2' => 2]],
                [['V1' => 1, 'RQ1' => 'K1', 'V2' => 2]]
            ],
            $testCallbacks->getArgsStack()
        );
    }

    /**
     * @expectedException        DomainException
     * @expectedExceptionMessage Match required
     */
    public function testNoMatchFoundButOneIsRequired()
    {
        $obj           = new Processing;
        $testCallbacks = new TestCallbacks;
        $obj->addCallback('RQ1', [$testCallbacks, 'setArgs']);
        $obj->addCallback('RQ2', [$testCallbacks, 'setArgs']);
        $obj->setMatchRequired(true);
        $obj->setUniqueMatchRequired(false);
        $obj->setData([
            'V1'  => 1,
            'NO1' => 'N1',
            'NO2' => 'N2',
            'V2'  => 2
        ]);
        $obj->process();
    }

    /**
     * @expectedException        DomainException
     * @expectedExceptionMessage Unique match required
     */
    public function testUniqueMatchRequiredButMoreThanOneMatch()
    {
        $obj           = new Processing;
        $testCallbacks = new TestCallbacks;
        $obj->addCallback('RQ1', [$testCallbacks, 'setArgs']);
        $obj->addCallback('RQ2', [$testCallbacks, 'setArgs']);
        $obj->setMatchRequired(true);
        $obj->setUniqueMatchRequired(true);
        $obj->setData([
            'V1'  => 1,
            'RQ1' => 'N1',
            'RQ2' => 'N2',
            'V2'  => 2
        ]);
        $obj->process();
    }
}
// EOF
