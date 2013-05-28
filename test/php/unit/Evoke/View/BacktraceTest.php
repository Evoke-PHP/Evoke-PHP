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
	 * Ensure that the view of an empty backtrace is good.
	 *
	 * @covers Evoke\View\Backtrace::get
	 */
	public function testGetEmtpy()
	{
		$obj = new Backtrace;
        $obj->setData($this->getMock('Evoke\Model\Data\DataIface'));
		$this->assertSame(['ol', ['class' => 'Backtrace'], []],	$obj->get());
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
        $obj->setData($data);

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
			$obj->get());
	}	
}
// EOF