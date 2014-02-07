<?php
namespace Evoke_Test\Network\URI\Rule;

use Evoke\Network\URI\Rule\RegexNamed,
	PHPUnit_Framework_TestCase;

class RegexNamedTest extends PHPUnit_Framework_TestCase
{
	/*********/
	/* Tests */
	/*********/

	/**
	 * @covers Evoke\Network\URI\Rule\RegexNamed::__construct
	 */
	public function testCreate()
	{
		$object = new RegexNamed('/match/', 'replacement');
		$this->assertInstanceOf('Evoke\Network\URI\Rule\RegexNamed', $object);
	}

	/**
	 * @covers Evoke\Network\URI\Rule\RegexNamed::getController
	 */
	public function testGetController()
	{
		$object = new RegexNamed('/match/', 'replacement');
		$this->assertSame('uri/replacementes/ok',
		                  $object->getController('uri/matches/ok'));
	}

	/**
	 * @covers Evoke\Network\URI\Rule\RegexNamed::getParams
	 */
	public function testGetParams()
	{
		$object = new RegexNamed('/m(...)a(?<Named>N.*)fin/', 'rep');
		$this->assertSame(['Named' => 'NamedMatch'],
		                  $object->getParams('m123aNamedMatchfin'));
	}

	/**
	 * @covers Evoke\Network\URI\Rule\RegexNamed::isMatch
	 */
	public function testIsMatchFalse()
	{
		$object = new RegexNamed('/m(...)a(?<Named>N.*)fin/', 'rep');
		$this->assertFalse($object->isMatch('maNamedNoMatchFun'));
	}

	/**
	 * @covers Evoke\Network\URI\Rule\RegexNamed::isMatch
	 */
	public function testIsMatchTrue()
	{
		$object = new RegexNamed('/m\d[A-G]/', 'rep');
		$this->assertTrue($object->isMatch('m1G'));
	}

}
// EOF