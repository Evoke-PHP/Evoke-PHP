<?php
namespace Evoke_Test\View;

use Evoke\View\Element,
	PHPUnit_Framework_TestCase;

/**
 *  @covers Evoke\View\Element
 */
class ElementTest extends PHPUnit_Framework_TestCase
{ 
	/**
	 * Ensure that a Element View can be constructed.
	 *
	 * @covers Evoke\View\Element::__construct
	 */	  
	public function test__constructGood()
	{
		$obj = new Element('div');
		$this->assertInstanceOf('Evoke\View\Element', $obj);
		$this->assertInstanceOf('Evoke\View\ViewIface', $obj);
	}

	/**
	 * Ensure that a bad tag is raised as an exception.
	 *
	 * @covers            Evoke\View\Element::__construct
	 * @expectedException InvalidArgumentException
	 */	  
	public function test__constructBad()
	{
		$obj = new Element(array('BAD'));
	}

	/**
	 * Ensure that the view of an empty element is good.
	 *
	 * @covers Evoke\View\Element::get
	 */
	public function testEmtpy()
	{
		$obj = new Element('div');
		$this->assertSame(['div', [], NULL], $obj->get());
	}

	/**
	 * Ensure that the attributes from construction are used by the view.
	 *
	 * @covers Evoke\View\Element::__construct
	 * @covers Evoke\View\Element::get
	 */
	public function testAttributes()
	{
		$attribs = ['class' => 'Overriden', 'other' => 'special'];
		$obj = new Element('form', $attribs);
		$this->assertSame(['form', $attribs, NULL],	$obj->get());
	}

	/**
	 * Ensure the elements are formatted correctly.
	 *
	 * @covers Evoke\View\Element::get
	 */
	public function testChildren()
	{
		$children = [['div', ['class' => 'one'], '1'],
		             ['p',   ['class' => 'two'], '2']];
			      
		$obj = new Element('div', [], $children);
		$this->assertSame(['div', [], $children], $obj->get());
	}	
}
// EOF