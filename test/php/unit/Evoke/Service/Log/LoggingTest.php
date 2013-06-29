<?php
namespace Evoke_Test\Service;

use Evoke\Service\Log\Logging,
    PHPUnit_Framework_TestCase;

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
	public function test__construct()
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

	/**
	 * Detaching something that isn't attached is not an error.
	 *
	 * @covers Evoke\Service\Log\Logging::detach
	 * @covers Evoke\Service\Log\Logging::log
	 */
	public function testDetachUnattached()
	{
		$logger = $this->getMock('Evoke\Service\Log\LoggerIface');
		$logger
			->expects($this->never())
			->method('log');
		
		$object = new Logging($this->getMock('DateTime'));
		$object->detach($logger);
		$object->log('any', E_USER_WARNING);
	}

	/**
	 * We don't log to detached observers.
	 *
	 * @covers Evoke\Service\Log\Logging::attach
	 * @covers Evoke\Service\Log\Logging::detach
	 */
	public function testDontLogToDetached()
	{
		$dateTime = $this->getMock('DateTime');
		$expectedMessage = 'This is the message.';
		$expectedLevel = 'User Error';
		$detached = 3;
		$object = new Logging($dateTime);
		$observers = [];
		
		for ($i = 0; $i < 4; $i++)
		{
			$observers[$i] = $this->getMock('Evoke\Service\Log\LoggerIface');
			if ($i === $detached)
			{
				$observers[$i]
					->expects($this->never())
					->method('log');
			}
			else
			{
				$observers[$i]
					->expects($this->once())
					->method('log')
					->with($dateTime, $expectedMessage, $expectedLevel);
			}
			
			$object->attach($observers[$i]);
		}

		$object->detach($observers[$detached]);
		$object->log('This is the message.', E_USER_ERROR);		
	}
}
// EOF