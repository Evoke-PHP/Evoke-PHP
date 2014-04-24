<?php
namespace Evoke_Test\Writer;

use Evoke\Writer\Factory,
	PHPUnit_Framework_TestCase;

/**
 * @covers Evoke\Writer\Factory
 * @uses   Evoke\Writer\Writer
 */
class FactoryTest extends PHPUnit_Framework_TestCase
{
	/*********/
	/* Tests */
	/*********/

	/**
	 * Create a JSON writer.
	 */
	public function testCreateJSON()
	{
		$object = new Factory;
		$this->assertInstanceOf('Evoke\Writer\JSON', $object->create('JSON'));
	}

	/**
	 * Create a HTML writer.
	 *
	 * @uses Evoke\Writer\XML
	 */
	public function testCreateHTML()
	{
		$object = new Factory;
		$this->assertInstanceOf('Evoke\Writer\XML', $object->create('HTML5'));
	}

	/**
	 * Create a Text writer.
	 */
	public function testCreateText()
	{
		$object = new Factory;
		$this->assertInstanceOf('Evoke\Writer\Text', $object->create('Text'));
	}

	/**
	 * Create an XHTML writer.
	 *
	 * @uses Evoke\Writer\XML
	 */
	public function testCreateXHTML()
	{
		$object = new Factory;
		$this->assertInstanceOf('Evoke\Writer\XML', $object->create('XHTML'));
	}

	/**
	 * Create an XML writer.
	 *
	 * @uses Evoke\Writer\XML
	 */
	public function testCreateXML()
	{
		$object = new Factory;
		$this->assertInstanceOf('Evoke\Writer\XML', $object->create('XML'));
	}

	/**
	 * Creating an unkown writer throws
	 *
	 * @expectedException DomainException
	 */
	public function testCreateUnknown()
	{
		$object = new Factory;
		$object->create('UnkownOutputFormat');
	}
}
// EOF