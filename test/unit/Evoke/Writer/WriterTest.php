<?php
namespace Evoke_Test\Writer;

use Evoke\Writer\Writer,
	PHPUnit_Framework_TestCase;

class WriterNonAbstract extends Writer
{
	// Provide the write function so we can be non-abstract.
	public function write($data)
	{
		$this->buffer .= 'NA' . $data;
	}	 
}

/**
 * @covers Evoke\Writer\Writer
 */
class WriterTest extends PHPUnit_Framework_TestCase
{
	/*********/
	/* Tests */
	/*********/

	/**
	 * Create an object.
	 */
	public function testCreate()
	{
		$object = new WriterNonAbstract;
		$this->assertInstanceOf('Evoke\Writer\Writer', $object);
	}

	/**
	 * The writer can be cleaned. 
	 */
	public function testClean()
	{
		$object = new WriterNonAbstract;
		$object->write('SOMETHING');
		$object->clean();
		$this->assertSame('', (string)$object);
	}
	
	/**
	 * Flushing sends the output and cleans the buffer.
	 */
	public function testFlush()
	{
		$object = new WriterNonAbstract;
		$object->writeStart();
		$object->write('Something to Flush');

		ob_start();
		$object->flush();
		$output = ob_get_contents();
		ob_end_clean();

		$this->assertSame('NASomething to Flush', $output);
		$this->assertSame('', (string)$object);
	}
	
	/**
	 * Buffer starts empty even after initialization.
	 */
	public function testStartsEmpty()
	{
		$object = new WriterNonAbstract;
		$object->writeStart();
		$this->assertSame('', (string)$object);
	}

	/**
	 * Text can be added to the writer and returned.
	 */
	public function testWriting()
	{
		$object = new WriterNonAbstract;
		$object->write('YO DUDE');
		$this->assertSame('NAYO DUDE', (string)$object);
	}

	/**
	 * End of writing can be called.
	 */
	public function testWritingEnd()
	{
		$object = new WriterNonAbstract;
		$object->write('YO DUDE');
		$object->writeEnd();
		$this->assertSame('NAYO DUDE', (string)$object);
	}
}
// EOF