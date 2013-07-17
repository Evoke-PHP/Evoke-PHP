<?php
namespace Evoke_Test\Service\Autoload;

use Evoke\Service\Autoload\PSR0Namespace,
	PHPUnit_Framework_TestCase,
	org\bovigo\vfs\vfsStream;

class PSR0NamespaceTest extends PHPUnit_Framework_TestCase
{
	/*********/
	/* Tests */
	/*********/

	/**
	 * Create an object.
	 *
	 * @covers Evoke\Service\Autoload\PSR0Namespace::__construct
	 */
	public function testCreate()
	{
		$this->assertInstanceOf('Evoke\Service\Autoload\PSR0Namespace',
		                        new PSR0Namespace('/BaseDir', 'Namespace'));
	}

	/**
	 * Load a class that exists.
	 *
	 * @covers Evoke\Service\Autoload\PSR0Namespace::load
	 */
	public function testLoadExists()
	{		
		vfsStream::setup(
			'root',
			NULL,
			['PSR0NamespaceTestNS' =>
			 ['A' =>
			  ['B.php' => '<?php namespace PSR0NamespaceTestNS\A; class B {}',
			   'C.php' => '<?php']]]);

		$object = new PSR0Namespace(vfsStream::url('root'),
		                            'PSR0NamespaceTestNS');

		$this->assertFalse(class_exists('PSR0NamespaceTestNS\A\B', FALSE));
		$object->load('PSR0NamespaceTestNS\A\B');
		$this->assertTrue(class_exists('PSR0NamespaceTestNS\A\B', FALSE));
	}

	/**
	 * Try to load a class that is outside the namespace.
	 *
	 * @covers Evoke\Service\Autoload\PSR0Namespace::load
	 */
	public function testLoadNonExistant()
	{
		vfsStream::setup(
			'root',
			NULL,
			['NS' =>
			 ['A' =>
			  ['B.php' => '<?php class BPSRLoadExists_XYZ {}',
			   'C.php' => '<?php']]]);

		$object = new PSR0Namespace(vfsStream::url('root'), 'NS');
		$object->load('NS\A\D');
		$this->assertFalse(class_exists('NS\A\D', FALSE));
	}

	/**
	 * Try to load a class that is outside the namespace.
	 *
	 * @covers Evoke\Service\Autoload\PSR0Namespace::load
	 */
	public function testLoadOutside()
	{
		vfsStream::setup(
			'root',
			NULL,
			['NS' =>
			 ['A' =>
			  ['B.php' => '<?php class BPSRLoadExists_XYZ {}',
			   'C.php' => '<?php']]]);

		$object = new PSR0Namespace(vfsStream::url('root'), 'NS');
		$object->load('NS\A\D');
		$this->assertFalse(class_exists('NS\A\D', FALSE));
	}

	
}
// EOF