<?php
namespace Evoke_Test\View\XHTML;

use Evoke\View\XHTML\Head,
	PHPUnit_Framework_TestCase;

/**
 * @covers Evoke\View\XHTML\Head
 */
class HeadTest extends PHPUnit_Framework_TestCase
{
	/*********/
	/* Tests */
	/*********/

	/**
	 * Create an object.
	 */
	public function testCreate()
	{
		$object = new Head('desc', 'key', 'title');
		$this->assertInstanceOf('Evoke\View\XHTML\Head', $object);
	}

	/**
	 * Get the view of the head.
	 */
	public function testGetView()
	{
		$object = new Head('DESC', 'KEY', 'TITLE', ['A.CSS'], ['B.JS', 'C.JS']);
		$this->assertSame(
			['head',
			 [],
			 [['title', [], 'TITLE'],
			  ['meta',
			   ['content' => 'TITLE',
			    'name' => 'title']],
			  ['meta',
			   ['content' => 'DESC',
			    'name' => 'description']],
			  ['meta',
			   ['content' => 'KEY',
			    'name' => 'keywords']],
			  ['link',
			   ['type' => 'text/css',
			    'href' => 'A.CSS',
			    'rel' => 'stylesheet']],
			  ['script',
			   ['type' => 'text/javascript',
			    'src' => 'B.JS']],
			  ['script',
			   ['type' => 'text/javascript',
			    'src' => 'C.JS']]]],
			$object->get());
	}
}
// EOF