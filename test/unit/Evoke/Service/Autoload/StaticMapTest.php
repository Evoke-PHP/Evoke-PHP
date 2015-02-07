<?php
namespace Evoke_Test\Service\Autoload;

use Evoke\Service\Autoload\StaticMap;
use org\bovigo\vfs\vfsStream;
use PHPUnit_Framework_TestCase;

/**
 * @covers Evoke\Service\Autoload\StaticMap
 */
class StaticMapTest extends PHPUnit_Framework_TestCase
{
    /*********/
    /* Tests */
    /*********/

    /**
     * Create an object.
     */
    public function testCreate()
    {
        $object = new StaticMap([]);
        $this->assertInstanceOf('Evoke\Service\Autoload\StaticMap', $object);
    }

    /**
     * We load the class if it exists.
     */
    public function testLoadExisting()
    {
        $classToLoad = 'StaticMapTestLoadExistingA_ZXY';
        vfsStream::setup(
            'root',
            null,
            [
                'a.php' => '<?php class ' . $classToLoad . ' {}',
                'b.php' => '<?php class B {}'
            ]
        );

        $object = new StaticMap(
            [
                'B'          => vfsStream::url('/b.php'),
                $classToLoad => vfsStream::url('root/a.php')
            ]
        );
        $object->load($classToLoad);

        $this->assertTrue(class_exists($classToLoad, false));
    }

    /**
     * We throw if the class doesn't exist.
     *
     * @expectedException RuntimeException
     */
    public function testLoadNonExistent()
    {
        vfsStream::setup(
            'root',
            null,
            ['a.php' => '<?php']
        );
        $object = new StaticMap([
            'A' => vfsStream::url('a.php'),
            'B' => vfsStream::url('NonExistant.php')
        ]);
        $object->load('B');
    }

    /**
     * We ignore items that are not covered by the map.
     */
    public function testLoadUncovered()
    {
        vfsStream::setup(
            'StaticMapTestLoadUncovered',
            null,
            ['a.php' => '<?php']
        );
        $object = new StaticMap([
            'A' => vfsStream::url('a.php'),
            'B' => vfsStream::url('Covered.php')
        ]);
        $object->load('StaticMapTestLoadUncoveredC_XYZ');
        $this->assertFalse(class_exists('StaticMapTestLoadUncoveredC_XYZ', false));
    }
}
// EOF
