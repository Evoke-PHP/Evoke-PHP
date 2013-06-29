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
	 * An exception is thrown if we are trying to show the exception without a
	 * view.
	 *
	 * @covers            Evoke\Service\ExceptionHandler::__construct
	 * @expectedException InvalidArgumentException
	 */
	public function testCreateBad()
	{
		$object = new ExceptionHandler(
			$this->getMock('Evoke\Network\HTTP\ResponseIface'),
			TRUE,
			$this->getMock('Evoke\Writer\WriterIface'));
	}

	/**
	 * An object can be created.
	 *
	 * @covers Evoke\Service\ExceptionHandler::__construct
	 */
	public function testCreateWithView()
	{
		$object = new ExceptionHandler(
			$this->getMock('Evoke\Network\HTTP\ResponseIface'),
			TRUE,
			$this->getMock('Evoke\Writer\WriterIface'),
			$this->getMock('Evoke\View\ExceptionIface'));

		$this->assertInstanceOf('Evoke\Service\ExceptionHandler', $object);
	}

	/**
	 * A view is only required if we are showing the exception.
	 *
	 * @covers Evoke\Service\ExceptionHandler::__construct
	 */
	public function testCreateWithoutView()
	{
		$object = new ExceptionHandler(
			$this->getMock('Evoke\Network\HTTP\ResponseIface'),
			FALSE,
			$this->getMock('Evoke\Writer\WriterIface'));

		$this->assertInstanceOf('Evoke\Service\ExceptionHandler', $object);
	}

	/**
	 * The handler can respond to an exception even if the writer isn't empty.
	 *
	 * @covers Evoke\Service\ExceptionHandler::handler
	 */
	public function testReponseWithFlush()
	{
		$this->doTestResponse(true);
	}

	/**
	 * The handler can respond to an exception.
	 *
	 * @covers Evoke\Service\ExceptionHandler::handler
	 */
	public function testReponseWithoutFlush()
	{
		$this->doTestResponse(false);
	}

	/*******************/
	/* Private Methods */
	/*******************/

	private function doTestResponse(/* Bool */ $withFlush)
	{
		$exception = new \Exception('This is it.');
		
		$responseIndex = 0;
		$response = $this->getMock('Evoke\Network\HTTP\ResponseIface');
		$response
			->expects($this->at($responseIndex++))
			->method('setStatus')
			->with(500);			
		$response
			->expects($this->at($responseIndex++))
			->method('setBody')
			->with('whatever the writer says goes.');
		$response
			->expects($this->at($responseIndex++))
			->method('send');
		
		$writerIndex = 0;
		$writer = $this->getMock('Evoke\Writer\PageIface');

		if ($withFlush)
		{
			$writer
				->expects($this->at($writerIndex++))
				->method('__toString')
				->with()
				->will($this->returnValue('NotEmpty'));
			$writer
				->expects($this->at($writerIndex++))
				->method('flush');
		}
		else
		{
			$writer
				->expects($this->at($writerIndex++))
				->method('__toString')
				->with()
				->will($this->returnValue(''));
		}
		
		$writer
			->expects($this->at($writerIndex++))
			->method('writeStart');		   
		$writer
			->expects($this->at($writerIndex++))
			->method('write')
			->with(['div',
			        ['class' => 'Message_Box System Exception'],
			        [
				        ['div', ['class' => 'Title'], 'System Error'],
				        ['div',
				         ['class' => 'Description'],
				         'The administrator has been notified.'],
				        ['div', [], 'View Element']]]);
		$writer
			->expects($this->at($writerIndex++))
			->method('writeEnd');
		$writer
			->expects($this->at($writerIndex++))
			->method('__toString')
			->with()
			->will($this->returnValue('whatever the writer says goes.'));
		
		$viewIndex = 0;
		$view = $this->getMock('Evoke\View\ExceptionIface');
		$view
			->expects($this->at($viewIndex++))
			->method('setException')
			->with($exception);
		$view
			->expects($this->at($viewIndex++))
			->method('get')
			->will($this->returnValue(['div', [], 'View Element']));

		$object = new ExceptionHandler($response, TRUE, $writer, $view);
		$object->handler($exception);
	}
}
// EOF