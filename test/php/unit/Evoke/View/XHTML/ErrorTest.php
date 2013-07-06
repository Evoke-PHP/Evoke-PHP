<?php
namespace Evoke_Test\View\XHTML;

use Evoke\View\XHTML\Error,
	PHPUnit_Framework_TestCase;

class ErrorTest extends PHPUnit_Framework_TestCase
{
	/******************/
	/* Data Providers */
	/******************/

	/*********/
	/* Tests */
	/*********/

	/**
	 * Create an object.
	 *
	 * @covers Evoke\View\XHTML\Error::__construct
	 */
	public function testCreate()
	{
		$object = new Error;
		$this->assertInstanceOf('Evoke\View\XHTML\Error', $object);
	}

	/**
	 * Get the view of an error.
	 *
	 * @covers Evoke\View\XHTML\Error::get
	 * @covers Evoke\View\XHTML\Error::getTypeString
	 * @covers Evoke\View\XHTML\Error::setError
	 */
	public function testGetView()
	{
		$object = new Error('<UNK>');
		$object->setError(['file' => 'FILE',
		                   'line' => 245,
		                   'type' => E_USER_ERROR]);

		$this->assertSame(
			['div',
			 ['class' => 'Error'],
			 [['div',
			   ['class' => 'Details'],
			   [['span', ['class' => 'Type'], 'E_USER_ERROR'],
			    ['span', ['class' => 'File'], 'FILE'],
			    ['span', ['class' => 'Line'], 245]]],
			  ['p', ['class' => 'Message'], '<UNK>']]],
			$object->get());
	}

	/**
	 * Unknown errors can still be dealt with.
	 *
	 * @covers Evoke\View\XHTML\Error::get
	 * @covers Evoke\View\XHTML\Error::getTypeString
	 * @covers Evoke\View\XHTML\Error::setError
	 */
	public function testUnknownError()
	{
		$object = new Error('WHO KNOWS');
		$object->setError(['file'    => 'F',
		                   'line'    => 2,
		                   'message' => 'BLAH',
		                   'type'    => -1]);

		$this->assertSame(
			['div',
			 ['class' => 'Error'],
			 [['div',
			   ['class' => 'Details'],
			   [['span', ['class' => 'Type'], 'WHO KNOWS'],
			    ['span', ['class' => 'File'], 'F'],
			    ['span', ['class' => 'Line'], 2]]],
			  ['p', ['class' => 'Message'], 'BLAH']]],
			$object->get());
	}

	/**
	 * If the error has not been set then it throws.
	 *
	 * @covers            Evoke\View\XHTML\Error::get
	 * @expectedException LogicException
	 */
	public function testUnsetError()
	{
		$object = new Error;
		$object->get();
	}
}
// EOF