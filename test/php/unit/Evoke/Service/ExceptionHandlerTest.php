<?php
namespace Evoke_Test\Service;

use Evoke\Service\ExceptionHandler,
    PHPUnit_Framework_TestCase;

/**
 *  @covers Evoke\Service\ExceptionHandler
 */
class ExceptionHandlerTest extends PHPUnit_Framework_TestCase
{
	/*********/
	/* Tests */
	/*********/

	/**
	 * Ensure that an object can be created.
	 *
	 * @covers Evoke\Service\ExceptionHandler::__construct
	 */
	public function test__construct()
	{
		$object = new ExceptionHandler(
			$this->getMock('Evoke\Service\Log\LoggingIface'),
			TRUE,
			1000,
			$this->getMock('Evoke\HTTP\ResponseIface'),
			$this->getMock('Evoke\Writer\WriterIface'));

		$this->assertInstanceOf('Evoke\Service\ExceptionHandler', $object);
	}
}
// EOF