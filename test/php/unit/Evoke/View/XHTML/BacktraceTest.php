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
	/******************/
	/* Data Providers */
	/******************/

	public function providerGoodBacktraceElements()
	{
		return [
			'One_Level' =>
			['Backtrace' => [['class'    => 'One',
			                  'file'     => 'one.php',
			                  'function' => 'oneUp',
			                  'line'     => 111,
			                  'type'     => 'one_type']],
			 'Expected'  =>
			 ['ol',
			  ['class' => 'Backtrace'],
			  [['li',
			    [],
			    [['span', ['class' => 'File'], 'one.php'],
			     ['span', ['class' => 'Line'], '(111)'],
			     ['span', ['class' => 'Class'], 'One'],
			     ['span', ['class' => 'Type'], 'one_type'],
			     ['span', ['class' => 'Function'], 'oneUp']]]]]],
			'Two_Level' =>
			['Backtrace' => [['class'    => 'Funky',
			                  'file'     => 'Funk.php',
			                  'function' => 'funkItUp',
			                  'line'     => 78,
			                  'type'     => 'typed'],
			                 ['class'    => 'Boogie',
			                  'file'     => 'Boog.php',
			                  'function' => 'boogieItUp',
			                  'type'     => 'btyped']],
			 'Expected'  =>
			 ['ol',
			  ['class' => 'Backtrace'],
			  [['li',
			    [],
			    [['span', ['class' => 'File'],     'Funk.php'],
			     ['span', ['class' => 'Line'],     '(78)'],
			     ['span', ['class' => 'Class'],    'Funky'],
			     ['span', ['class' => 'Type'],     'typed'],
			     ['span', ['class' => 'Function'], 'funkItUp']]],
			   ['li',
			    [],
			    [['span', ['class' => 'File'],     'Boog.php'],
			     ['span', ['class' => 'Class'],    'Boogie'],
			     ['span', ['class' => 'Type'],     'btyped'],
			     ['span', ['class' => 'Function'], 'boogieItUp']]]]]
				]];
	}

	/*********/
	/* Tests */
	/*********/
	
	/**
	 * @covers 		 Evoke\View\XHTML\Backtrace::get
	 * @covers 		 Evoke\View\XHTML\Backtrace::set
	 * @dataProvider providerGoodBacktraceElements
	 */
	public function testGoodBacktraceElements($backtrace, $expected)
	{
		$obj = new Backtrace;
		$obj->set($backtrace);
		$this->assertSame($expected, $obj->get());
	}

	/**
	 * @covers 					 Evoke\View\XHTML\Backtrace::get
	 * @expectedException        LogicException
	 * @expectedExceptionMessage needs backtrace.
	 */
	public function testGetEmtpy()
	{
		$obj = new Backtrace;
		$obj->get();
	}
}
// EOF