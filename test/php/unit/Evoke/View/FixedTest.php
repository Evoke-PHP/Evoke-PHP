<?php
namespace Evoke_Test\View;

use Evoke\View\Fixed,
	PHPUnit_Framework_TestCase;

class FixedTest extends PHPUnit_Framework_TestCase
{
	/******************/
	/* Data Providers */
	/******************/

	public function providerFixed()
	{
		return [
			'Integer' => [125],
			'Array'   => [['div', [], 'aiofw']],
			'String'  => ['str']];
	}
	
	/*********/
	/* Tests */
	/*********/

	/**
	 * Create an object.
	 *
	 * @covers Evoke\View\Fixed::__construct
	 */
	public function testCreate()
	{
		$object = new Fixed('blah');
		$this->assertInstanceOf('Evoke\View\Fixed', $object);
	}

	/**
	 * The fixed view returns the fixed data sent to it.
	 *
	 * @covers       Evoke\View\Fixed::get
	 * @dataProvider providerFixed
	 */
	public function testGetView($value)
	{
		$object = new Fixed($value);
		$this->assertSame($value, $object->get());
	}
}
// EOF