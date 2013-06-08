<?php
namespace Evoke_Test\Controller;

use Evoke\Controller\Controller,
    PHPUnit_Framework_TestCase;

/**
 * Test the base controller (and the abstract parts of it).
 *
 * @covers Evoke\Controller\Controller
 * @covers Evoke\Controller\ControllerAbstract
 */
class ControllerTest extends PHPUnit_Framework_TestCase
{	
	/*********/
	/* Tests */
	/*********/
	
	/**
	 * Test the construction of a controller.
	 *
	 * @covers Evoke\Controller\Controller::__construct
	 */
	public function test__constructGood()
	{
		$mocks = $this->getMocks();
		$object = new Controller('', [], [], $mocks['Response'],
		                         $mocks['Writer'], $mocks['View']);

		$this->assertInstanceOf('Evoke\Controller\Controller', $object);
	}

	/**
	 * Test the execution of a page based controller.
	 */
	public function testExecutePageBased()
	{
		$mocks = $this->getMocks();
		$outputFormat = 'HTML';
		$pageSetup = [];
		$viewOutput = ['div', [], 'View Output'];
		$wIndex = 0;

		$mocks['Response']
			->expects($this->at(0))
			->method('setBody')
			->with($mocks['Writer']);
		$mocks['Response']
			->expects($this->at(1))
			->method('send');

		$mocks['View']
			->expects($this->at(0))
			->method('get')
			->will($this->returnValue($viewOutput));

		$mocks['Writer']
			->expects($this->at($wIndex++))
			->method('writeStart')
			->with($pageSetup);
		$mocks['Writer']
			->expects($this->at($wIndex++))
			->method('write')
			->with($viewOutput);
		$mocks['Writer']
			->expects($this->at($wIndex++))
			->method('WriteEnd');
		
		$object = new Controller(
			$outputFormat, $pageSetup, [], $mocks['Response'],
			$mocks['Writer'], $mocks['View']);
		$object->execute();
	}
	 
	public function testErrorForRequiredClean()
	{
		$errorRecord = [];
		$errorExpected = [
			'Number'  => E_USER_WARNING,
			'Message' => 'Writer is required to be clean, found "UNCLEAN ' .
			'WRITER CONTENT" flushing and continuing.'];
		$mocks = $this->getMocks();
		$outputFormat = 'TEXT';
		$pageSetup = [];
		$uncleanContent = 'UNCLEAN WRITER CONTENT';
		$viewOutput = 'View Output';

		$mocks['Response']
			->expects($this->at(0))
			->method('setBody')
			->with($mocks['Writer']);
		$mocks['Response']
			->expects($this->at(1))
			->method('send');

		$mocks['View']
			->expects($this->at(0))
			->method('get')
			->will($this->returnValue($viewOutput));

		$mocks['Writer']
			->expects($this->at(0))
			->method('__toString')
			->will($this->returnValue($uncleanContent));
		$mocks['Writer']
			->expects($this->at(1))
			->method('write')
			->with($viewOutput);

		set_error_handler(
			function($errno , $errstr) use (&$errorRecord)
			{
				$errorRecord = [
					'Number'  => $errno,
					'Message' => $errstr];
			});
		
		$object = new Controller(
			$outputFormat, $pageSetup, [], $mocks['Response'],
			$mocks['Writer'], $mocks['View']);
		$object->execute();

		$this->assertEquals(
			$errorExpected, $errorRecord,
			'Warnings should be emitted when a clean writer is required but ' .
			'not provided.');
	}
	
	/*******************/
	/* Private Methods */
	/*******************/
	
	private function getMocks()
	{
		$writerMock = $this->getMockBuilder('Evoke\Writer\XHTML')
			->disableOriginalConstructor()
			->getMock();
		
		return [
			'Response' => $this->getMock('Evoke\HTTP\ResponseIface'),
			'Writer'   => $writerMock,
			'View'     => $this->getMock('Evoke\View\View')];
	}	
}
// EOF