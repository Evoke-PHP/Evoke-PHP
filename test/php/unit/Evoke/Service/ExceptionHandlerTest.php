<?php
namespace Evoke_Test\Service;

use Evoke\Service\ExceptionHandler,
    PHPUnit_Framework_TestCase;

class ExceptionHandlerTest extends PHPUnit_Framework_TestCase
{
	/******************/
	/* Data Providers */
	/******************/

	public function providerConditions()
	{
		return ['NoFlush_NoShow' => [FALSE, FALSE],
		        'NoFlush_DoShow' => [FALSE, TRUE],
		        'DoFlush_NoSHow' => [TRUE, FALSE],
		        'DoFlush_DoShow' => [TRUE, TRUE]];
	}
	
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
			$this->getMock('Evoke\View\MessageBoxIface'),
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
			$this->getMock('Evoke\View\MessageBoxIface'),
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
			$this->getMock('Evoke\View\MessageBoxIface'),
			$this->getMock('Evoke\Writer\WriterIface'));

		$this->assertInstanceOf('Evoke\Service\ExceptionHandler', $object);
	}

	/**
	 * The handler can respond to an exception even if the writer isn't empty.
	 *
	 * @covers Evoke\Service\ExceptionHandler::handler
	 * @dataProvider providerConditions
	 */
	public function testReponse(/* Bool */ $requiresFlush,
	                            /* Bool */ $showException)
	{
		$this->doTestResponse($requiresFlush, $showException);
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

	private function doTestResponse(/* Bool */ $requiresFlush,
	                                /* Bool */ $showException)
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

		if ($requiresFlush)
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
			->with(['div', [], 'MBOX is the big wig.']);
		$writer
			->expects($this->at($writerIndex++))
			->method('writeEnd');
		$writer
			->expects($this->at($writerIndex++))
			->method('__toString')
			->with()
			->will($this->returnValue('whatever the writer says goes.'));
 
		
		$viewExceptionIndex = 0;
		$viewException = $this->getMock('Evoke\View\ExceptionIface');

		if ($showException)
		{
			$viewException
				->expects($this->at($viewExceptionIndex++))
				->method('setException')
				->with($exception);
			$viewException
				->expects($this->at($viewExceptionIndex++))
				->method('get')
				->will($this->returnValue(
					       ['div', [], 'Exception View Element']));
		}
		else
		{
			$viewException
				->expects($this->never())
				->method('setException');
			$viewException
				->expects($this->never())
				->method('get');
		}

		$viewMessageBoxIndex = 0;
		$viewMessageBox = $this->getMock('Evoke\View\MessageBoxIface');
		$viewMessageBox
			->expects($this->at($viewMessageBoxIndex++))
			->method('addContent')
			->with(['div',
			        ['class' => 'Description'],
			        'The administrator has been notified.']);

		if ($showException)
		{
			$viewMessageBox
				->expects($this->at($viewMessageBoxIndex++))
				->method('addContent')
				->with(['div', [], 'Exception View Element']);
		}

		$viewMessageBox
			->expects($this->at($viewMessageBoxIndex++))
			->method('get')
			->will($this->returnValue(['div', [], 'MBOX is the big wig.']));
		
		$object = new ExceptionHandler(
			$response, $showException, $viewMessageBox, $writer,
			$viewException);
		$object->handler($exception);
	}
}
// EOF