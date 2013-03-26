<?php
namespace Evoke_Test\View;

use Evoke\View\Backtrace,
	PHPUnit_Framework_TestCase;

/**
 *  @covers Evoke\View\Backtrace
 */
class BacktraceTest extends PHPUnit_Framework_TestCase
{ 
	/**
	 * Ensure that a Backtrace View can be constructed.
	 *
	 * @covers Evoke\View\Backtrace::__construct
	 */	  
	public function test__constructGood()
	{
		$obj = new Backtrace;
		$this->assertInstanceOf('Evoke\View\Backtrace', $obj);
		$this->assertInstanceOf('Evoke\View\ViewIface', $obj);
	}

	/**
	 * Ensure that the view of an empty backtrace is good.
	 *
	 * @covers Evoke\View\Backtrace::get
	 */
	public function testGetEmtpy()
	{
		$obj = new Backtrace;
		$this->assertSame(
			['ol', ['class' => 'Backtrace'], []],
			$obj->get($this->getMock('Evoke\Model\Data\DataIface')));
	}

	/**
	 * Ensure that the attributes from construction are used by the view.
	 *
	 * @covers Evoke\View\Backtrace::__construct
	 * @covers Evoke\View\Backtrace::get
	 */
	public function testAttributes()
	{
		$attribs = ['class' => 'Overriden', 'other' => 'special'];
		$obj = new Backtrace($attribs);
		$this->assertSame(
			['ol', $attribs, []],
			$obj->get($this->getMock('Evoke\Model\Data\DataIface')));
	}

	/**
	 * Ensure the backtrace elements are formatted correctly.
	 *
	 * @covers Evoke\View\Backtrace::get
	 */
	public function testBacktraceElements()
	{
		$data = new \Evoke\Model\Data\Data(
			array(0 => array('Class'    => 'Funky',
			                 'File'     => 'Funk.php',
			                 'Function' => 'funkItUp',
			                 'Line'     => 78,
			                 'Type'     => 'typed'),
			      1 => array('Class'    => 'Boogie',
			                 'File'     => 'Boog.php',
			                 'Function' => 'boogieItUp',
			                 'Type'     => 'btyped')));
		$obj = new Backtrace;

		$this->assertSame(
			['ol',
			 ['class' => 'Backtrace'],
			 [
				 ['li',
				  [],
				  [
					  ['span', ['class' => 'File'],     'Funk.php'],
					  ['span', ['class' => 'Line'],     '(78)'],
					  ['span', ['class' => 'Class'],    'Funky'],
					  ['span', ['class' => 'Type'],     'typed'],
					  ['span', ['class' => 'Function'], 'funkItUp']
					  ]],
				 ['li',
				  [],
				  [
					  ['span', ['class' => 'File'],     'Boog.php'],
					  ['span', ['class' => 'Class'],    'Boogie'],
					  ['span', ['class' => 'Type'],     'btyped'],
					  ['span', ['class' => 'Function'], 'boogieItUp']
					  ]]]],
			$obj->get($data));
	}	
}
// EOF