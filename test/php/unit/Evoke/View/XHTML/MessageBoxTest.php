<?php
namespace Evoke_Test\View\XHTML;

use Evoke\View\XHTML\MessageBox,
	PHPUnit_Framework_TestCase;

class MessageBoxTest extends PHPUnit_Framework_TestCase
{
	/*********/
	/* Tests */
	/*********/

	/**
	 * Create an object.
	 *
	 * @covers Evoke\View\XHTML\MessageBox::__construct
	 */
	public function testCreate()
	{
		$object = new MessageBox;
		$this->assertInstanceOf('Evoke\View\XHTML\MessageBox', $object);
	}

	/**
	 * Build a message box and get it.
	 *
	 * @covers Evoke\View\XHTML\MessageBox::addContent
	 * @covers Evoke\View\XHTML\MessageBox::get
	 * @covers Evoke\View\XHTML\MessageBox::setTitle
	 */
	public function testBuildAndGet()
	{
		$object = new MessageBox(['class' => 'Test Message_Box Info']);
		$object->setTitle('Test Box');
		$object->addContent(['div', [], 'One']);
		$object->addContent('Text');

		$this->assertSame(
			['div',
			 ['class' => 'Test Message_Box Info'],
			 [['div', ['class' => 'Title'], 'Test Box'],
			  ['div',
			   ['class' => 'Content'],
			   [['div', [], 'One'],
			    'Text']]]],
			$object->get());
	}
}
// EOF