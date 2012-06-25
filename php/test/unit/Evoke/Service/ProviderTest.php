<?php
namespace Evoke_Test\Service;

use Evoke\Service\Cache,
	Evoke\Service\Provider,
	PHPUnit_Framework_TestCase;

// Interface for the test.
interface PI
{

}

// Classes for the test.
class PA
{
	// No constructor means no dependencies.
}

class PB
{
	public function __construct(PI $paramOne, Array $arr) {}
}

class PC implements PI
{
	public function __construct($int, $strDefault = 'Paul') {}
}

class PD
{
	public function __construct(PA $concrete, PI $interface) {}
}

/**
 * Provider Test.
 */
class ProviderTest extends PHPUnit_Framework_TestCase
{
	/**
	 * Test the construction.
	 *
	 * @covers Evoke\Service\Provider::__construct
	 */
	public function test__construct()
	{
		$this->assertInstanceOf(
			'Evoke\Service\Provider',
			new Provider($this->getMock('Evoke\Service\Cache')));
	}

	/**
	 * Test the successful making of objects.
	 *
	 * @covers 		 Evoke\Service\Provider::make
	 * @covers 		 Evoke\Service\Provider::getDependency
	 * @covers 		 Evoke\Service\Provider::getReflection
	 * @covers 		 Evoke\Service\Provider::convertPascalToCamel
	 * @depends      Evoke_Test\Service\ProviderTest::test__construct
	 * @dataProvider providerMake
	 */
	public function testMake($object, $classname, $params)
	{
		$this->assertInstanceOf($classname, $object->make($classname, $params));
	}

	/******************/
	/* Data Providers */
	/******************/

	public function providerMake()
	{
		$tests = array();

		// A simple concrete class with no dependencies.
		$tests['Simple_Concrete'] = array(
			'Object'    => new Provider($this->getMock('Evoke\Service\Cache')),
			'Classname' => 'Evoke_Test\Service\PA',
			'Params'    => array());

		// A class which includes an object which has dependencies.
		$tests['Dependencies_Down_Graph'] = array(
			'Object'    => new Provider($this->getMock('Evoke\Service\Cache')),
			'Classname' => 'Evoke_Test\Service\PB',
			'Params'    => array(
				'Param_One' => $this->getMock(
					'Evoke_Test\Service\PC', NULL, array(1)),
				'Arr'       => array('yay', 'array')));

		// A class with a default value that should be used.
		$tests['Uses_Default'] = array(
			'Object'    => new Provider($this->getMock('Evoke\Service\Cache')),
			'Classname' => 'Evoke_Test\Service\PC',
			'Params'    => array('Int' => 9));

		// A class with a concrete dependency.
		$tests['Concrete_Dependency'] = array(
			'Object'    => new Provider($this->getMock('Evoke\Service\Cache')),
			'Classname' => 'Evoke_Test\Service\PD',
			'Params'    => array('Interface' => $this->getMock(
				                     'Evoke_Test\Service\PC', NULL, array(1))));
		
		return $tests;
	}
}

// EOF
