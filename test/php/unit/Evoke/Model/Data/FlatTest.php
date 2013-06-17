<?php
namespace Evoke_Test\Model\Data\FlatTest;

use Evoke\Model\Data\Flat,
	PHPUnit_Framework_TestCase;

class FlatTest extends PHPUnit_Framework_TestCase
{ 
	/******************/
	/* Data Providers */
	/******************/
	
	/*********/
	/* Tests */
	/*********/

	/**
	 * Test that data can be accessed by field.
	 *
	 * @covers Evoke\Model\Data\Flat::offsetGet
	 */
	public function testOffsetGet()
	{
		$object = new Flat;
		$object->setData([['One' => 1, 'Two' => 2],
		                  ['One' => 8, 'Two' => 9]]);
		
		$this->assertEquals(1, $object['One']);
	}
}
// EOF
