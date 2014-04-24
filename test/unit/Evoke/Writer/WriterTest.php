<?php
namespace Evoke_Test\Writer;

use Evoke\Writer\Writer,
	PHPUnit_Framework_TestCase;

class WriterNonAbstract extends Writer
{
	// Provide the write function so we can be non-abstract.
	public function write($data) {}	 
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
}
// EOF