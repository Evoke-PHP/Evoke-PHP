<?php
namespace Evoke_Test\Service;

use Evoke\Service\Log\Logging,
    PHPUnit_Framework_TestCase;

/**
 *  @covers Evoke\Service\Log\Logging
 */
class LoggingTest extends PHPUnit_Framework_TestCase
{
	/*********/
	/* Tests */
	/*********/

	/**
	 * We can create an object.
	 *
	 * @covers Evoke\Service\Log\Logging::__construct
	 */
	public function testCreate()
	{
		$object = new Logging($this->getMock('DateTime'));
		$this->assertInstanceOf('Evoke\Service\Log\Logging', $object);
	}

	/**
	 * We can log to attached observers.
	 *
	 * @covers Evoke\Service\Log\Logging::attach
	 * @covers Evoke\Service\Log\Logging::log
	 */
	public function testCanLogToAttached()
	{
		$dateTime = $this->getMock('DateTime');
		$expectedMessage = 'This is the message.';
		$expectedLevel = 'User Error';
		$object = new Logging($dateTime);

		for ($i = 0; $i < 3; $i++)
		{
			$observer = $this->getMock('Evoke\Service\Log\LoggerIface');
			$observer
				->expects($this->once())
				->method('log')
				->with($dateTime, $expectedMessage, $expectedLevel);

			$object->attach($observer);
		}

		$object->log('This is the message.', E_USER_ERROR);
	}
}
// EOF