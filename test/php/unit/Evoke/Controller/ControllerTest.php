<?php
namespace Evoke_Test\Controller;

use Evoke\Controller\Controller,
    PHPUnit_Framework_TestCase;

/**
 * Test the base controller (and the abstract parts of it).
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
	 * @covers Evoke\Controller\ControllerAbstract::__construct
	 */
	public function test__constructGood()
	{
		$mocks = $this->getMocks();
		$object = new Controller(
			[], $mocks['Response'], $mocks['Writer'], $mocks['View']);
		$this->assertInstanceOf('Evoke\Controller\Controller', $object);
	}

	/**
	 * Test the execution of a page based controller.
	 *
	 * @covers Evoke\Controller\Controller::execute
	 */
	public function testExecutePageBased()
	{
		$viewOutput = ['div', [], 'View Output'];

		$rIndex = 0;
		$response = $this->getMock('Evoke\Network\HTTP\ResponseIface');
		$response
			->expects($this->at($rIndex++))
			->method('setBody')
			->with('Writer Output');
		$response
			->expects($this->at($rIndex++))
			->method('send');

		$view = $this->getMock('Evoke\View\ViewIface');
		$view
			->expects($this->at(0))
			->method('get')
			->will($this->returnValue($viewOutput));

		$wIndex = 0;
		$writer = $this->getMock('Evoke\Writer\PageIface');
		$writer
			->expects($this->at($wIndex++))
			->method('__toString')
			->will($this->returnValue(''));
		$writer
			->expects($this->at($wIndex++))
			->method('isPageBased')
			->will($this->returnValue(TRUE));
		$writer
			->expects($this->at($wIndex++))
			->method('writeStart');
		$writer
			->expects($this->at($wIndex++))
			->method('write')
			->with($viewOutput);
		$writer
			->expects($this->at($wIndex++))
			->method('writeEnd');
		$writer
			->expects($this->at($wIndex++))
			->method('__toString')
			->will($this->returnValue('Writer Output'));

		$object = new Controller([], $response, $writer, $view);
		$object->execute();
	}

	/**
	 * We clean a writer if we require a clean writer.
	 *
	 * @covers Evoke\Controller\Controller::execute
	 * @covers Evoke\Controller\ControllerAbstract::requireCleanWriter
	 */
	public function testErrorForRequiredClean()
	{
		$errorRecord = [];
		$errorExpected = [
			'Number'  => E_USER_WARNING,
			'Message' => 'Writer is required to be clean, found "UNCLEAN ' .
			'WRITER CONTENT" cleaning and continuing.'];
		$mocks = $this->getMocks();
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

		$wIndex = 0;
		$mocks['Writer']
			->expects($this->at($wIndex++))
			->method('__toString')
			->will($this->returnValue($uncleanContent));
		$mocks['Writer']
			->expects($this->at($wIndex++))
			->method('clean');
		$mocks['Writer']
			->expects($this->at($wIndex++))
			->method('isPageBased')
			->will($this->returnValue(FALSE));			          
		$mocks['Writer']
			->expects($this->at($wIndex++))
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
			[], $mocks['Response'], $mocks['Writer'], $mocks['View']);
		$object->execute();

		$this->assertSame(
			$errorExpected, $errorRecord,
			'Warnings should be emitted when a clean writer is required but ' .
			'not provided.');
	}
	
	/*******************/
	/* Private Methods */
	/*******************/
	
	private function getMocks()
	{
		return [
			'Response' => $this->getMock('Evoke\Network\HTTP\ResponseIface'),
			'Writer'   => $this->getMock('Evoke\Writer\PageIface'),
			'View'     => $this->getMock('Evoke\View\ViewIface')];
	}	
}
// EOF