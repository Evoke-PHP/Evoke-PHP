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
		$object = new Head(
            [['rel' => 'stylesheet', 'href' => 'styles.css']],
            ['description' => 'Head Test Meta Description'],
            'Head Test Title',
            [['style', ['type' => 'text/css'], 'body { background: #F00; }']]);
		$this->assertInstanceOf('Evoke\View\XHTML\Head', $object);
	}

	/**
	 * Get the view of the head.
	 */
	public function testGetView()
	{
		$object = new Head(
            [['rel' => 'stylesheet', 'href' => 'styles.css']],
            ['description' => 'Head Test Meta Description',
             'keywords'    => 'Head Keywords'],
            'Head Test Title',
            [['style', ['type' => 'text/css'], 'body { background: #F00; }']]);
		$this->assertSame(
			['head',
			 [],
			 [['title', [], 'Head Test Title'],
			  ['meta',
			   ['name'    => 'description',
			    'content' => 'Head Test Meta Description']],
			  ['meta',
			   ['name'    => 'keywords',
                'content' => 'Head Keywords']],
			  ['link',
			   ['rel'  => 'stylesheet',
			    'href' => 'styles.css']],
			  ['style',
			   ['type' => 'text/css'],
               'body { background: #F00; }']]],
			$object->get());
	}
}
// EOF