<?php
namespace Evoke_Test\Service\Autoload;

use Evoke\Service\Autoload\StaticMap,
	PHPUnit_Framework_TestCase,
	org\bovigo\vfs\vfsStream;

class StaticMapTest extends PHPUnit_Framework_TestCase
{
	/******************/
	/* Data Providers */
	/******************/

	public function providerLoadExisting()
	{
		return [
			'Flat' => ['Filesystem' => ['a.php' => '<?php class LoadExistingA_ZXY {}', 'b.php' => '<?php $b = 2'],
			           'Classname'  => 'LoadExistingA_ZXY',
			           'Map'        => ['LoadExistingA_ZXY' => vfsStream::url('a.php'), 'B' => '/b.php']]];
	}
	
	/*********/
	/* Tests */
	/*********/

	/**
	 * Create an object.
	 *
	 * @covers Evoke\Service\Autoload\StaticMap::__construct
	 */
	public function testCreate()
	{
		$object = new StaticMap([]);
		$this->assertInstanceOf('Evoke\Service\Autoload\StaticMap', $object);
	}

	/**
	 * We load the class if it exists.
	 *
	 * @covers       Evoke\Service\Autoload\StaticMap::load
	 * @dataProvider providerLoadExisting
	 */
	public function testLoadExisting(
		Array $filesystem, /* String */ $classname, Array $map)
	{
		vfsStream::setup('testLoadExisting', NULL, $filesystem);
		$object = new StaticMap($map);
		$object->load($classname);

		$this->assertTrue(class_exists($classname, FALSE));
	}

	/**
	 * We throw if the class doesn't exist.
	 *
	 * @covers            Evoke\Service\Autoload\StaticMap::load
	 * @expectedException RuntimeException
	 */
	public function testLoadNonExistant()
	{
		vfsStream::setup(
			'testLoadNonExistant',
			NULL,
			array('a.php' => '<?php'));
		$object = new StaticMap(['A' => vfsStream::url('a.php'),
		                         'B' => vfsStream::url('NonExistant.php')]);
		$object->load('B');
	}

	/**
	 * We ignore items that are not covered by the map.
	 *
	 * @covers Evoke\Service\Autoload\StaticMap::load
	 */
	public function testLoadUncovered()
	{
		vfsStream::setup(
			'testLoadUncovered',
			NULL,
			array('a.php' => '<?php'));
		$object = new StaticMap(['A' => vfsStream::url('a.php'),
		                         'B' => vfsStream::url('Covered.php')]);
		$object->load('TestLoadUncoveredC_XYZ');
		$this->assertFalse(class_exists('TestLoadUncoveredC_XYZ', FALSE));
	}	
}
// EOF