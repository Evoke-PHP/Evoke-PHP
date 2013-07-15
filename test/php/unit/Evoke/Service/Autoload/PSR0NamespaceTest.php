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
			'testPSRLoadExists',
			NULL,
			['dir' =>
			 ['NS' =>
			  ['A' =>
			   ['B.php' => '<?php class BPSRLoadExists_XYZ {}',
			    'C.php' => '<?php']]]]);

		$object = new PSR0Namespace(vfsStream::url('dir'), 'NS');

		$this->assertFalse(class_exists('BPSRLoadExists_XYZ', FALSE));
		$object->load('NS\A\B');
		$this->assertTrue(class_exists('BPSRLoadExists_XYZ', FALSE));
	}

	/**
	 * Try to load a class that is outside the namespace.
	 *
	 * @covers Evoke\Service\Autoload\PSR0Namespace::load
	 */
	public function testLoadNonExistant()
	{
		vfsStream::setup(
			'testPSRLoadNonExistant',
			NULL,
			['dir' =>
			 ['NS' =>
			  ['A' =>
			   ['B.php' => '<?php class BPSRLoadExists_XYZ {}',
			    'C.php' => '<?php']]]]);

		$object = new PSR0Namespace(vfsStream::url('dir'), 'NS');
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
			'testPSRLoadOutside',
			NULL,
			['dir' =>
			 ['NS' =>
			  ['A' =>
			   ['B.php' => '<?php class BPSRLoadExists_XYZ {}',
			    'C.php' => '<?php']]]]);

		$object = new PSR0Namespace(vfsStream::url('dir'), 'NS');
		$object->load('BS\A\D');
		$this->assertFalse(class_exists('BS\A\D', FALSE));
	}

	
}
// EOF