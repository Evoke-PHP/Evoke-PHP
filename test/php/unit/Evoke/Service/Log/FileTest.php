<?php
namespace Evoke_Test\Service\Log;

use Evoke\Service\Log\File,
	PHPUnit_Framework_TestCase;

class FileTest extends PHPUnit_Framework_TestCase
{
	/******************/
	/* Data Providers */
	/******************/

	/*********/
	/* Tests */
	/*********/

	/**
	 * Create an object.
	 *
	 * @covers Evoke\Service\Log\File::__construct
	 */
	public function testCreate()
	{
		$object = new File('Filename');
		$this->assertInstanceOf('Evoke\Service\Log\File', $object);
	}
}
// EOF