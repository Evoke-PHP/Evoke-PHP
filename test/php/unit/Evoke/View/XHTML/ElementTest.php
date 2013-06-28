<?php
namespace Evoke_Test\View\XHTML;

use Evoke\Model\Data\Flat,
    Evoke\View\XHTML\Element,
	PHPUnit_Framework_TestCase;

/**
 *  @covers Evoke\View\XHTML\Element
 */
class ElementTest extends PHPUnit_Framework_TestCase
{ 
	/**
	 * Ensure that a Element View can be constructed.
	 *
	 * @covers Evoke\View\XHTML\Element::__construct
	 */	  
	public function test__constructGood()
	{
		$obj = new Element('div');
		$this->assertInstanceOf('Evoke\View\XHTML\Element', $obj);
		$this->assertInstanceOf('Evoke\View\ViewIface', $obj);
	}

	/**
	 * Ensure that the attributes from construction are used by the view.
	 *
	 * @covers Evoke\View\XHTML\Element::__construct
	 * @covers Evoke\View\XHTML\Element::get
	 */
	public function testAttributes()
	{
		$attribs = ['class' => 'Overriden', 'other' => 'special'];
		$obj = new Element('form', $attribs);
		$this->assertSame(['form', $attribs, []], $obj->get());
	}

	/**
	 * Ensure that data is passed to the child view.
	 *
	 * @covers Evoke\View\XHTML\Element::get
	 */
	public function testChildren()
	{        
		$children = [0 => ['div', ['class' => 'one'], '1'],
                     1 => ['p',   ['class' => 'two'], '2']];
        $viewChild = $this->getMock('Evoke\View\Data');
        $dataChildren = new Flat;
        $dataChildren->setData($children);

        foreach ($children as $index => $child)
        {
            $viewChild
                ->expects($this->at(2 * $index))
                ->method('setData')
                ->with($dataChildren);
            
            $viewChild
                ->expects($this->at((2 * $index) + 1))
                ->method('get')
                ->with()
                ->will($this->returnValue($child));
        }
        
			      
		$obj = new Element('div', [], $viewChild);
        $obj->setData($dataChildren);
		$this->assertSame(['div', [], $children], $obj->get());
	}	

	/**
	 * Ensure that the view of an empty element is good.
	 *
	 * @covers Evoke\View\XHTML\Element::get
	 */
	public function testEmtpy()
	{
		$obj = new Element('div');
		$this->assertSame(['div', [], []], $obj->get());
	}
}
// EOF