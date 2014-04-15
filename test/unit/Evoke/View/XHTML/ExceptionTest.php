<?php
namespace Evoke_Test\View\XHTML;

use Evoke\View\XHTML\Exception,
	PHPUnit_Framework_TestCase;

class ExceptionTest extends PHPUnit_Framework_TestCase
{
	/*********/
	/* Tests */
	/*********/

	/**
	 * Get the view.
	 *
	 * @covers Evoke\View\XHTML\Exception::get
	 * @covers Evoke\View\XHTML\Exception::set
	 */
	public function testGetView()
	{
		$testException = new \Exception('Created in test.');
		$object = new Exception;
		$object->set($testException);
		$expected = ['div',
		             ['class' => 'Exception'],
		             [['div', ['class' => 'Type'], 'Exception'],
		              ['p', ['class' => 'Message'], 'Created in test.'],
		              ['pre', ['class' => 'Trace'], $testException->getTraceAsString()]]];

		$this->assertSame($expected, $object->get());
	}

	/**
	 * Unset exception causes throw.
	 *
	 * @covers            Evoke\View\XHTML\Exception::get
	 * @expectedException LogicException
	 */
	public function testUnsetException()
	{
		$object = new Exception;
		$object->get();
	}

}
// EOF