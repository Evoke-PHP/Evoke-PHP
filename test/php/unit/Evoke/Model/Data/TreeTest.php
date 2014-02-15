<?php
namespace Evoke_Test\Model\Data;

use Evoke\Model\Data\Tree,
	PHPUnit_Framework_TestCase;

class TreeTest extends PHPUnit_Framework_TestCase
{
	/******************/
	/* Data Providers */
	/******************/

	public function providerGetChildren()
	{
		return ['None' => [[]],
		        'One'  => [[$this->getMock('Evoke\Model\Data\TreeIface')]],
		        'More' => [[$this->getMock('Evoke\Model\Data\TreeIface'),
		                    $this->getMock('Evoke\Model\Data\TreeIface'),
		                    $this->getMock('Evoke\Model\Data\TreeIface')]]];
	}
	
	public function providerHasChildren()
	{
		return ['None' => [[], false],
		        'One'  => [[$this->getMock('Evoke\Model\Data\TreeIface')],
		                   true],
		        'More' => [[$this->getMock('Evoke\Model\Data\TreeIface'),
		                    $this->getMock('Evoke\Model\Data\TreeIface'),
		                    $this->getMock('Evoke\Model\Data\TreeIface')],
		                   true]];		                    
	}
	
	public function providerUseValue()
	{
		return ['Array'  => [[1, '2', new \StdClass]],
		        'Int'    => [1],
		        'String' => ['blah'],
		        'Object' => [new \StdClass]];
	}
	
	/*********/
	/* Tests */
	/*********/

	/**
	 * @covers       Evoke\Model\Data\Tree::add
	 * @covers       Evoke\Model\Data\Tree::getChildren
	 * @dataProvider providerGetChildren
	 */
	public function testGetChildren(Array $children, $expected)
	{
		$obj = new Tree;

		foreach ($children as $child)
		{
			$obj->add($child);
		}

		$this->assertSame($children, $obj->getChildren());
	}
	
	/**
	 * @covers       Evoke\Model\Data\Tree::add
	 * @covers       Evoke\Model\Data\Tree::hasChildren
	 * @dataProvider providerHasChildren
	 */
	public function testHasChildren(Array $children, $expected)
	{
		$obj = new Tree;

		foreach ($children as $child)
		{
			$obj->add($child);
		}

		$this->assertSame($expected, $obj->hasChildren());
	}
	
	/**
	 * @covers       Evoke\Model\Data\Tree::get
	 * @covers       Evoke\Model\Data\Tree::set
	 * @dataProvider providerUseValue
	 */
	public function testUseValue($value)
	{
		$obj = new Tree;
		$obj->set($value);
		
		$this->assertSame($value, $obj->get());		
	}
}
// EOF