<?php
namespace Evoke_Test\View\XHTML;

use Evoke\Model\Data\Flat,
	Evoke\View\XHTML\Backtrace,
	PHPUnit_Framework_TestCase;

/**
 *  @covers Evoke\View\XHTML\Backtrace
 */
class BacktraceTest extends PHPUnit_Framework_TestCase
{ 
	/**
	 * Ensure the backtrace elements are formatted correctly.
	 *
	 * @covers Evoke\View\XHTML\Backtrace::get
	 */
	public function testBacktraceElements()
	{
		$data = new Flat;
		$data->setData(
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

	/**
	 * Ensure that the view of an empty backtrace is good.
	 *
	 * @covers Evoke\View\XHTML\Backtrace::get
	 */
	public function testGetEmtpy()
	{
		$obj = new Backtrace;
        $obj->setData($this->getMock('Evoke\Model\Data\DataIface'));
		$this->assertSame(['ol', ['class' => 'Backtrace'], []],	$obj->get());
	}

	/**
	 * Ensure that trying to get the view with the data unset throws.
	 *
	 * @covers            Evoke\View\XHTML\Backtrace::get
	 * @expectedException LogicException
	 */
	public function testUnsetData()
	{
		$obj = new Backtrace;
		$obj->get();
	}
}
// EOF