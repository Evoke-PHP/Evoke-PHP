<?php
namespace Evoke_Test\Service;

use Evoke\Service\ErrorHandler;
use PHPUnit_Framework_TestCase;

/**
 * @covers Evoke\Service\ErrorHandler
 */
class ErrorHandlerTest extends PHPUnit_Framework_TestCase
{
    protected $savedErrorReporting;

    /***********/
    /* Fixture */
    /***********/

    public function setUp()
    {
        $this->savedErrorReporting = error_reporting();
    }

    public function tearDown()
    {
        error_reporting($this->savedErrorReporting);
    }

    /*********/
    /* Tests */
    /*********/

    /**
     * We can create an object.
     */
    public function testCreate()
    {
        $object = new ErrorHandler($this->getMock('Evoke\Service\Log\LoggingIface'));
        $this->assertInstanceOf('Evoke\Service\ErrorHandler', $object);
    }

    /**
     * Non reportable errors should not be reported.
     */
    public function testNonReportableError()
    {
        $logging = $this->getMock('Evoke\Service\Log\LoggingIface');
        $logging
            ->expects($this->never())
            ->method('log');

        $object = new ErrorHandler($logging);

        // Turn off error reporting so that the error should not be reportable.
        error_reporting(0);

        // Ensure the error handler returns TRUE which stops the builtin error
        // handler from running on non-reportable errors.
        $this->assertTrue($object->handler(1, 'ErrorString', 'ErrorFile', 2, []));
    }

    /**
     * Recoverable errors are logged and converted to an ErrorException.
     *
     * @expectedException ErrorException
     */
    public function testRecoverAfterLogging()
    {
        $logging = $this->getMock('Evoke\Service\Log\LoggingIface');
        $logging
            ->expects($this->once())
            ->method('log')
            ->with('EString in EFile on 2', E_RECOVERABLE_ERROR);

        // Ensure we log all.
        error_reporting(-1);

        $object = new ErrorHandler($logging);
        $object->handler(E_RECOVERABLE_ERROR, 'EString', 'EFile', 2, []);
    }

    /**
     * We can report a complex error (with context).
     */
    public function testReportComplex()
    {
        $logging = $this->getMock('Evoke\Service\Log\LoggingIface');
        $logging
            ->expects($this->once())
            ->method('log')
            ->with('EString in EFile on 2 context: ' . print_r(['Yob'], true), E_USER_ERROR);

        // Ensure we log all.
        error_reporting(-1);

        $object = new ErrorHandler($logging, true);
        $this->assertTrue($object->handler(E_USER_ERROR, 'EString', 'EFile', 2, ['Yob']));
    }

    /**
     * We can report a simple error (without context).
     */
    public function testReportSimple()
    {
        $logging = $this->getMock('Evoke\Service\Log\LoggingIface');
        $logging
            ->expects($this->once())
            ->method('log')
            ->with('EString in EFile on 2', E_WARNING);

        // Ensure we log all.
        error_reporting(-1);

        $object = new ErrorHandler($logging);
        $this->assertFalse($object->handler(E_WARNING, 'EString', 'EFile', 2, []));
    }
}
// EOF
