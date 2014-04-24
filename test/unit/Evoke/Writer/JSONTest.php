<?php
namespace Evoke_Test\Writer;

use Evoke\Writer\JSON,
	PHPUnit_Framework_TestCase;

/**
 * @covers Evoke\Writer\JSON
 * @uses   Evoke\Writer\Writer
 */
class JSONTest extends PHPUnit_Framework_TestCase
{
	/*********/
	/* Tests */
	/*********/

	/**
	 * Write JSON data.
	 */
	public function testWrite()
	{
		$object = new JSON;
		$object->write(['One' => 1, 'Two' => 'Dos']);

		$this->assertSame('{"One":1,"Two":"Dos"}', (string)$object);
	}
}
// EOF