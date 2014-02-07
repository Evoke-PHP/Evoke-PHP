<?php
namespace Evoke_Test\Network\URI\Rule;

use Evoke\Network\URI\Rule\UpperCaseFirst,
	PHPUnit_Framework_TestCase;

class UpperCaseFirstTest extends PHPUnit_Framework_TestCase
{
	/*********/
	/* Tests */
	/*********/

	/**
	 * @covers Evoke\Network\URI\Rule\UpperCaseFirst::__construct
	 */
	public function testCreate()
	{
		$obj = new UpperCaseFirst(['_']);
		$this->assertInstanceOf('Evoke\Network\URI\Rule\UpperCaseFirst', $obj);
	}

	/**
	 * @covers Evoke\Network\URI\Rule\UpperCaseFirst::getController
	 */
	public function testGetController()
	{
		$obj = new UpperCaseFirst(['_', ' ']);
		$this->assertSame('First LETTER_Uppercased',
		                  $obj->getController('first LETTER_uppercased'));
	}

	/**
	 * @covers Evoke\Network\URI\Rule\UpperCaseFirst::isMatch
	 */
	public function testIsMatchFalse()
	{
		$obj = new UpperCaseFirst(['/']);
		$this->assertFalse($obj->isMatch('thisDontMatch'));
	}

	/**
	 * @covers Evoke\Network\URI\Rule\UpperCaseFirst::isMatch
	 */
	public function testIsMatchTrue()
	{
		$obj = new UpperCaseFirst(['/']);
		$this->assertTrue($obj->isMatch('this/matches'));
	}
}
// EOF