<?php
namespace Evoke_Test\Service;

use Evoke\Service\Cache,
	Evoke\Service\Provider,
	PHPUnit_Framework_TestCase;

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

	/**
	 * Trying to make an object with a missing Abstract/Interface throws IAE.
	 *
	 * @covers  Evoke\Service\Provider::getDependency 
	 * @depends Evoke_Test\Service\ProviderTest::test__construct
	 * @expectedException InvalidArgumentException
	 * @expectedExceptionMessage Missing Abstract/Interface Dependency
	 */
	public function testMissingDependencyAbstractInterface()
	{
		$object = new Provider($this->getMock('Evoke\Service\CacheIface'));
		$object->make('Evoke_Test\Service\PD');
	}

	/**
	 * Trying to make an object with a missing Array throws IAE.
	 *
	 * @covers  Evoke\Service\Provider::getDependency 
	 * @depends Evoke_Test\Service\ProviderTest::test__construct
	 * @expectedException InvalidArgumentException
	 * @expectedExceptionMessage Missing Array Dependency for parameter: arr
	 */
	public function testMissingDependencyArray()
	{
		$object = new Provider($this->getMock('Evoke\Service\CacheIface'));
		$object->make('Evoke_Test\Service\PB',
		              array('Param_One' => $this->getMock(
			                    'Evoke_Test\Service\PC', NULL, array(1))));
	}

	/**
	 * Trying to make an object with a missing scalar throws IAE.
	 *
	 * @covers  Evoke\Service\Provider::getDependency 
	 * @depends Evoke_Test\Service\ProviderTest::test__construct
	 * @expectedException InvalidArgumentException
	 * @expectedExceptionMessage Missing Scalar Dependency
	 */
	public function testMissingDependencyScalar()
	{
		$object = new Provider($this->getMock('Evoke\Service\CacheIface'));
		$object->make('Evoke_Test\Service\PC');
	}
	
	/**
	 * Test that the reflection cache is set when it is fresh.
	 *
	 * @covers  Evoke\Service\Provider::getReflection
	 * @depends Evoke_Test\Service\ProviderTest::test__construct
	 */
	public function testReflectionCacheFresh()
	{
		$cache = $this->getMock('Evoke\Service\CacheIface');

		$i = 0;
		$cache->expects($this->at($i++))
			->method('exists')
			->with($this->equalTo('Evoke_Test\Service\PD'))
			->will($this->returnValue(false));

		$cache->expects($this->at($i++))
			->method('set')
			->with($this->equalTo('Evoke_Test\Service\PD'),
			       $this->arrayHasKey('Class'));
			
		
		$object = new Provider($cache);
		$object->make('Evoke_Test\Service\PD',
		              array('Interface' => $this->getMock(
			                    'Evoke_Test\Service\PC', NULL, array(1))));
	}

	/**
	 * Test that the reflection cache is used when it has information.
	 *
	 * @covers  Evoke\Service\Provider::getReflection
	 * @depends Evoke_Test\Service\ProviderTest::test__construct
	 */
	public function testReflectionCacheUsed()
	{	
		// Now simulate the cache being used.
		$usedCache = $this->getMock('Evoke\Service\CacheIface');

		$i = 0;
		$usedCache->expects($this->at($i++))
			->method('exists')
			->with($this->equalTo('Evoke_Test\Service\PD'))
			->will($this->returnValue(true));

		$pdClass = new \ReflectionClass('Evoke_Test\Service\PD');
		$pdParams = $pdClass->getConstructor()->getParameters();
		
		$usedCache->expects($this->at($i++))
			->method('get')
			->with($this->equalTo('Evoke_Test\Service\PD'))
			->will($this->returnValue(
				       array('Class'  => $pdClass,
				             'Params' => $pdParams)));
		
		$object = new Provider($usedCache);
		$this->assertInstanceOf(
			'Evoke_Test\Service\PD',
			$object->make('Evoke_Test\Service\PD',
			              array('Interface' => $this->getMock(
				                    'Evoke_Test\Service\PC', NULL, array(1)))));
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

		$tests['Built_In_Class'] = array(
			'Object'    => new Provider($this->getMock('Evoke\Service\Cache')),
			'Classname' => 'DateTime',
			'Params'    => array());
		
		return $tests;
	}
}

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


// EOF
