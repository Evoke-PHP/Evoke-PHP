<?php
namespace Evoke_Test\Writer;

use Evoke\Writer\JSON,
	PHPUnit_Framework_TestCase;

class JSONTest extends PHPUnit_Framework_TestCase
{
	/*********/
	/* Tests */
	/*********/

	/**
	 * Knows that it isn't page based.
	 *
	 * @covers Evoke\Writer\JSON::isPageBased
	 */
	public function testIsPageBased()
	{
		$object = new JSON;
		$this->assertFalse($object->isPageBased(), 'Should not be page based.');
	}

	/**
	 * Write JSON data.
	 *
	 * @covers Evoke\Writer\JSON::write
	 * @covers Evoke\Writer\Writer::__toString
	 */
	public function testWrite()
	{
		$object = new JSON;
		$object->write(['One' => 1, 'Two' => 'Dos']);

		$this->assertSame('{"One":1,"Two":"Dos"}', (string)$object);
	}
}
// EOF