<?php
namespace Evoke_Test\Controller;

use Evoke\Controller\ControllerAbstract,
	PHPUnit_Framework_TestCase;

class ControllerNonAbstract extends ControllerAbstract
{
	public function execute()
	{

	}
}

/**
 * @covers Evoke\Controller\ControllerAbstract
 */
class ControllerAbstractTest extends PHPUnit_Framework_TestCase
{
	/*********/
	/* Tests */
	/*********/

	public function testCreate()
	{
		$obj = new ControllerNonAbstract(
			'OutputFormat',
			[],
			$this->getMock('Evoke\Network\HTTP\ResponseIface'));
		$this->assertInstanceOf('Evoke\Controller\ControllerAbstract', $obj);
	}
	
}
// EOF