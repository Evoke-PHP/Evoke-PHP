<?php
namespace Evoke_Test\Service\Autoload;

use Evoke\Service\Autoload\PSR0Namespace;
use org\bovigo\vfs\vfsStream;
use PHPUnit_Framework_TestCase;

/**
 * @covers Evoke\Service\Autoload\PSR0Namespace
 * @uses   Evoke\Service\Autoload\AutoloadIface
 */
class PSR0NamespaceTest extends PHPUnit_Framework_TestCase
{
    /*********/
    /* Tests */
    /*********/

    /**
     * Create an object.
     */
    public function testCreate()
    {
        $this->assertInstanceOf('Evoke\Service\Autoload\PSR0Namespace', new PSR0Namespace('/BaseDir', 'Namespace'));
    }

    /**
     * Load a class that exists.
     */
    public function testLoadExists()
    {
        vfsStream::setup(
            'root',
            null,
            [
                'PSR0NamespaceTestNS' =>
                    [
                        'A' =>
                            [
                                'B.php' => '<?php namespace PSR0NamespaceTestNS\A; class B {}',
                                'C.php' => '<?php'
                            ]
                    ]
            ]
        );

        $object = new PSR0Namespace(vfsStream::url('root'), 'PSR0NamespaceTestNS');

        $this->assertFalse(class_exists('PSR0NamespaceTestNS\A\B', false));
        $object->load('PSR0NamespaceTestNS\A\B');
        $this->assertTrue(class_exists('PSR0NamespaceTestNS\A\B', false));
    }

    /**
     * Try to load a class that is outside the namespace.
     */
    public function testLoadNonExistent()
    {
        vfsStream::setup(
            'root',
            null,
            [
                'NS' =>
                    [
                        'A' =>
                            [
                                'B.php' => '<?php class BPSRLoadExists_XYZ {}',
                                'C.php' => '<?php'
                            ]
                    ]
            ]
        );

        $object = new PSR0Namespace(vfsStream::url('root'), 'NS');
        $object->load('NS\A\D');
        $this->assertFalse(class_exists('NS\A\D', false));
    }

    /**
     * Try to load a class with a namespace length the same as defined, but
     * different.
     */
    public function testLoadSameLengthButDifferent()
    {
        vfsStream::setup(
            'root',
            null,
            [
                'NS' =>
                    [
                        'A' =>
                            [
                                'B.php' => '<?php namespace NS\A; class B {}',
                                'C.php' => '<?php'
                            ]
                    ]
            ]
        );

        $object = new PSR0Namespace(vfsStream::url('root'), 'NS');
        $object->load('WS\A\B');
        $this->assertFalse(class_exists('WS\A\B', false));
    }

    /**
     * Try to load a class that is outside the namespace.
     */
    public function testLoadOutside()
    {
        vfsStream::setup(
            'root',
            null,
            [
                'NS' =>
                    [
                        'A' =>
                            [
                                'B.php' => '<?php class BPSRLoadExists_XYZ {}',
                                'C.php' => '<?php'
                            ]
                    ]
            ]
        );

        $object = new PSR0Namespace(vfsStream::url('root'), 'NS');
        $object->load('NS\A\D');
        $this->assertFalse(class_exists('NS\A\D', false));
    }
}
// EOF
