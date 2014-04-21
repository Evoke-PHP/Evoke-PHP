<?php
namespace Evoke_Test\Service;

use Evoke\Service\ExceptionHandler,
    PHPUnit_Framework_TestCase;

class ExceptionHandlerTest extends PHPUnit_Framework_TestCase
{
	protected $errors = [];
	protected $savedErrorReporting;
	
	/***********/
	/* Fixture */
	/***********/

	public function handleErrorByRecordingItForTest($errno, $errstr)
	{
		$this->errors[] = [$errno, $errstr];
	}

	public function setUp()
	{
		$this->errors = [];
		$this->savedErrorReporting = set_error_handler(
			[$this, 'handleErrorByRecordingItForTest']);
	}
	
	public function tearDown()
	{
		restore_error_handler();
	}

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
			$this->getMock('Evoke\View\XHTML\MessageBox'),
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
			$this->getMock('Evoke\View\XHTML\MessageBox'),
			$this->getMock('Evoke\Writer\WriterIface'),
			$this->getMock('Evoke\View\XHTML\Exception'));

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
			$this->getMock('Evoke\View\XHTML\MessageBox'),
			$this->getMock('Evoke\Writer\WriterIface'));

		$this->assertInstanceOf('Evoke\Service\ExceptionHandler', $object);
	}
	
	/**
	 * @covers       Evoke\Service\ExceptionHandler::handler
	 * @dataProvider providerConditions
	 */
	public function testHandleExceptionTriggersCorrectErrors(
		$requiresFlush, $showException)
	{
		$exception = new \Exception('This is it.');
		$expectedErrors = [[E_USER_WARNING, 'This is it.']];
		$response = $this->getMock('Evoke\Network\HTTP\ResponseIface');
		$writer = $this->getMock('Evoke\Writer\WriterIface');
		$viewException = $this->getMock('Evoke\View\XHTML\Exception');
		$viewMessageBox = $this->getMock('Evoke\View\XHTML\MessageBox');

		if ($requiresFlush)
		{
			$writer
				->expects($this->at(0))
				->method('__toString')
				->will($this->returnValue('NOT_EMPTY'));
			
			$expectedErrors[] =
				[E_USER_WARNING,
				 'Bufffer needs to be flushed in exception handler for ' .
				 'clean error page.  Buffer was: NOT_EMPTY'];
		}
		
		$object = new ExceptionHandler(
			$response, $showException, $viewMessageBox, $writer,
			$viewException);
		$object->handler($exception);

		$this->assertSame($expectedErrors, $this->errors);
	}
	
	/**
	 * @covers       Evoke\Service\ExceptionHandler::handler
	 * @dataProvider providerConditions
	 */
	public function testHandleException($requiresFlush, $showException)
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
		$writer = $this->getMock('Evoke\Writer\WriterIface');

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
			->method('writeStart')
			->with();
		$writer
			->expects($this->at($writerIndex++))
			->method('write')
			->with(['head',
			        [],
			        [['title', [], ['Uncaught Exception']]]]);
		$writer
			->expects($this->at($writerIndex++))
			->method('write')
			->with(['body',
			        [],
			        [['div', [], 'MBOX is the big wig.']]]);
		$writer
			->expects($this->at($writerIndex++))
			->method('writeEnd')
			->with();		
		$writer
			->expects($this->at($writerIndex++))
			->method('__toString')
			->with()
			->will($this->returnValue('whatever the writer says goes.'));
 
		
		$viewExceptionIndex = 0;
		$viewException = $this->getMock('Evoke\View\XHTML\Exception');

		if ($showException)
		{
			$viewException
				->expects($this->at($viewExceptionIndex++))
				->method('set')
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
				->method('set');
			$viewException
				->expects($this->never())
				->method('get');
		}

		$viewMessageBoxIndex = 0;
		$viewMessageBox = $this->getMock('Evoke\View\XHTML\MessageBox');
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