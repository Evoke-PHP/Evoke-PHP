<?php
namespace Evoke_Test\Service\Autoload;

use Evoke\Service\Autoload\StaticMap,
	PHPUnit_Framework_TestCase,
	org\bovigo\vfs\vfsStream;

class StaticMapTest extends PHPUnit_Framework_TestCase
{
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
	 * @covers Evoke\Service\Autoload\StaticMap::load
	 */
	public function testLoadExisting()
	{
		$classToLoad = 'StaticMapTestLoadExistingA_ZXY';
		vfsStream::setup(
			'root',
	        NULL,
	        ['a.php' => '<?php class ' . $classToLoad . ' {}',
	         'b.php' => '<?php class B {}']);

        $object = new StaticMap(
	        ['B'          => vfsStream::url('/b.php'),
	         $classToLoad => vfsStream::url('root/a.php')]);
        $object->load($classToLoad);

		$this->assertTrue(class_exists($classToLoad, FALSE));
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
			'root',
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
			'StaticMapTestLoadUncovered',
			NULL,
			array('a.php' => '<?php'));
		$object = new StaticMap(['A' => vfsStream::url('a.php'),
		                         'B' => vfsStream::url('Covered.php')]);
		$object->load('StaticMapTestLoadUncoveredC_XYZ');
		$this->assertFalse(class_exists('StaticMapTestLoadUncoveredC_XYZ',
		                                FALSE));
	}	
}
// EOF