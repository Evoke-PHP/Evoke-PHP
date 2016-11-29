<?php
namespace Evoke_Test\Service\Log;

use Evoke\Service\Log\Logging;
use PHPUnit_Framework_TestCase;

/**
 * @covers Evoke\Service\Log\Logging
 */
class LoggingTest extends PHPUnit_Framework_TestCase
{
    /*********/
    /* Tests */
    /*********/

    /**
     * We can create an object.
     */
    public function testConstruct()
    {
        $object = new Logging($this->createMock('DateTime'));
        $this->assertInstanceOf('Evoke\Service\Log\Logging', $object);
    }

    /**
     * We can log to attached observers.
     */
    public function testCanLogToAttached()
    {
        $dateTime        = new \DateTime;
        $expectedMessage = 'This is the message.';
        $expectedLevel   = 'User Notice';
        $object          = new Logging($dateTime);

        for ($i = 0; $i < 3; $i++) {
            $observer = $this->createMock('Evoke\Service\Log\LoggerIface');
            $observer
                ->expects($this->once())
                ->method('log')
                ->with($dateTime, $expectedMessage, $expectedLevel);

            $object->attach($observer);
        }

        $object->log('This is the message.', E_USER_NOTICE);
    }

    /**
     * Detaching something that isn't attached is not an error.
     */
    public function testDetachUnattached()
    {
        $logger = $this->createMock('Evoke\Service\Log\LoggerIface');
        $logger
            ->expects($this->never())
            ->method('log');

        $object = new Logging($this->createMock('DateTime'));
        $object->detach($logger);
        $object->log('any', E_USER_WARNING);
    }

    /**
     * We don't log to detached observers.
     */
    public function testDontLogToDetached()
    {
        $dateTime        = new \DateTime;
        $expectedMessage = 'This is the message.';
        $expectedLevel   = 'User Notice';
        $detached        = 3;
        $object          = new Logging($dateTime);
        $observers       = [];

        for ($i = 0; $i < 4; $i++) {
            $observers[$i] = $this->createMock('Evoke\Service\Log\LoggerIface');
            if ($i === $detached) {
                $observers[$i]
                    ->expects($this->never())
                    ->method('log');
            } else {
                $observers[$i]
                    ->expects($this->once())
                    ->method('log')
                    ->with($dateTime, $expectedMessage, $expectedLevel);
            }

            $object->attach($observers[$i]);
        }

        $object->detach($observers[$detached]);
        $object->log('This is the message.', E_USER_NOTICE);
    }
}
// EOF
