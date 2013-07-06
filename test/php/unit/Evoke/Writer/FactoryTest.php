<?php
namespace Evoke_Test\Writer;

use Evoke\Writer\Factory,
	PHPUnit_Framework_TestCase;

class FactoryTest extends PHPUnit_Framework_TestCase
{
	/*********/
	/* Tests */
	/*********/

	/**
	 * Create a JSON writer.
	 *
	 * @covers Evoke\Writer\Factory::create
	 */
	public function testCreateJSON()
	{
		$object = new Factory;
		$this->assertInstanceOf('Evoke\Writer\JSON', $object->create('JSON'));
	}

	/**
	 * Create a HTML writer.
	 *
	 * @covers Evoke\Writer\Factory::create
	 */
	public function testCreateHTML()
	{
		$object = new Factory;
		$this->assertInstanceOf('Evoke\Writer\XML', $object->create('HTML5'));
	}

	/**
	 * Create a Text writer.
	 *
	 * @covers Evoke\Writer\Factory::create
	 */
	public function testCreateText()
	{
		$object = new Factory;
		$this->assertInstanceOf('Evoke\Writer\Text', $object->create('Text'));
	}

	/**
	 * Create an XHTML writer.
	 *
	 * @covers Evoke\Writer\Factory::create
	 */
	public function testCreateXHTML()
	{
		$object = new Factory;
		$this->assertInstanceOf('Evoke\Writer\XML', $object->create('XHTML'));
	}

	/**
	 * Create an XML writer.
	 *
	 * @covers Evoke\Writer\Factory::create
	 */
	public function testCreateXML()
	{
		$object = new Factory;
		$this->assertInstanceOf('Evoke\Writer\XML', $object->create('XML'));
	}

	/**
	 * Creating an unkown writer throws
	 *
	 * @covers            Evoke\Writer\Factory::create
	 * @expectedException DomainException
	 */
	public function testCreateUnknown()
	{
		$object = new Factory;
		$object->create('UnkownOutputFormat');
	}
}
// EOF