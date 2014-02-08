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
		$object->setURI('uri/matches/ok');
		$this->assertSame('uri/replacementes/ok', $object->getController());
	}

	/**
	 * @covers Evoke\Network\URI\Rule\RegexNamed::getParams
	 */
	public function testGetParams()
	{
		$object = new RegexNamed('/m(...)a(?<Named>N.*)fin/', 'rep');
		$object->setURI('m123aNamedMatchfin');
		$this->assertSame(['Named' => 'NamedMatch'], $object->getParams());
	}

	/**
	 * @covers Evoke\Network\URI\Rule\RegexNamed::isMatch
	 */
	public function testIsMatchFalse()
	{
		$object = new RegexNamed('/m(...)a(?<Named>N.*)fin/', 'rep');
		$object->setURI('maNamedNoMatchFun');
		$this->assertFalse($object->isMatch());
	}

	/**
	 * @covers Evoke\Network\URI\Rule\RegexNamed::isMatch
	 */
	public function testIsMatchTrue()
	{
		$object = new RegexNamed('/m\d[A-G]/', 'rep');
		$object->setURI('m1G');
		$this->assertTrue($object->isMatch());
	}

}
// EOF