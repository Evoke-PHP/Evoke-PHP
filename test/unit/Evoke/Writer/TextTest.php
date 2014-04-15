<?php
namespace Evoke_Test\Writer;

use Evoke\Writer\Text,
	PHPUnit_Framework_TestCase;

/**
 * Text Writer and Abstract Writer test.
 */
class TextTest extends PHPUnit_Framework_TestCase
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
	 * @covers Evoke\Writer\Writer::__construct
	 */
	public function testCreate()
	{
		$object = new Text;
		$this->assertInstanceOf('Evoke\Writer\Text', $object);
	}

	/**
	 * The writer can be cleaned. 
	 *
	 * @covers Evoke\Writer\Writer::clean
	 */
	public function testClean()
	{
		$object = new Text;
		$object->write('SOMETHING');
		$object->clean();
		$this->assertSame('', (string)$object);
	}
	
	/**
	 * Flushing sends the output and cleans the buffer.
	 *
	 * @covers Evoke\Writer\Text::write
	 * @covers Evoke\Writer\Writer::flush
	 * @covers Evoke\Writer\Writer::writeStart
	 * @covers Evoke\Writer\Writer::__toString
	 */
	public function testFlush()
	{
		$object = new Text;
		$object->writeStart();
		$object->write('Something to Flush');

		ob_start();
		$object->flush();
		$output = ob_get_contents();
		ob_end_clean();

		$this->assertSame('Something to Flush', $output);
		$this->assertSame('', (string)$object);
	}
	
	/**
	 * Buffer starts empty even after initialization.
	 *
	 * @covers Evoke\Writer\Writer::__toString
	 * @covers Evoke\Writer\Writer::writeStart
	 */
	public function testStartsEmpty()
	{
		$object = new Text;
		$object->writeStart();
		$this->assertSame('', (string)$object);
	}

	/**
	 * Text can be added to the writer and returned.
	 *
	 * @covers Evoke\Writer\Text::write
	 * @covers Evoke\Writer\Writer::__toString
	 */
	public function testTextWriting()
	{
		$object = new Text;
		$object->write('YO DUDE');
		$this->assertSame('YO DUDE', (string)$object);
	}	
}
// EOF