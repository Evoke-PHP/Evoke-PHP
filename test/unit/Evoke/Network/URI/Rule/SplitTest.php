<?php
namespace Evoke_Test\Network\URI\Rule;

use Evoke\Network\URI\Rule\Split,
	PHPUnit_Framework_TestCase;

/**
 * @covers Evoke\Network\URI\Rule\Split
 * @uses   Evoke\Network\URI\Rule\Rule
 */
class SplitTest extends PHPUnit_Framework_TestCase
{
	/******************/
	/* Data Providers */
	/******************/

	public function providerController()
	{
		return [
			'One'       => ['ControllerName',
			                new Split('ControllerName', ['One'], 'Prefix', '+'),
			                'Prefix+Part'],
			'One_Empty' => ['CON',
			                new Split('CON', ['One'], 'CON+', '+'),
			                'CON+'],
			'Two'       => ['CTRL',
			                new Split('CTRL', ['P1', 'P2'], 'PRE\FIX', '\\'),
			                'PRE\FIX\Part1\Part2'],
			'Two_Empty' => ['ThisIsIt',
			                new Split('ThisIsIt', ['P1', 'P2'], 'C', '\\'),
			                'C\\']];
	}

	public function providerMatch()
	{
		return [
			'Empty_Prefix_1' => [true,
			                     new Split('DC', ['One'], '', '\\'),
			                     'Blah'],
			'Empty_Prefix_2' => [true,
			                     new Split('DC', ['One', 'Two'], '', '|'),
			                     'a|b'],
			'One_Empty' 	 => [true,
				                 new Split('DC', ['One'], 'CON+', '+'),
				                 'CON+'],
			'One_Extra' 	 => [false,
				                 new Split('Cont', ['One'], 'Cont ', ' '),
				                 'Cont One Two'],
			'One_Match' 	 => [true,
				                 new Split('DC', ['One'], 'Cont+', '+'),
				                 'Cont+Part'],
			'One_Prefix_Bad' => [false,
			                     new Split('C', ['One'], 'A', 'S'),
			                     'C'],
			'Two_Empty' 	 => [true,
				                 new Split('C', ['One', 'Two'], 'C**', '**'),
				                'C**A**'],
			'Two_Extra' 	 => [false,
				                 new Split('Cont',
				                           ['P1', 'P2'],
				                           'Cont\\',
				                           '\\'),
				                 'Cont\One\Two\Three'],
			'Two_Less'  	 => [false,
				                 new Split('Cont',
				                           ['P1', 'P2'],
				                           'Cont\\',
				                           '\\'),
				                 'Cont\One'],
			'Two_Match' 	 => [true,
				                 new Split('DC', ['P1', 'P2'], 'CTRL\\', '\\'),
				                'CTRL\Part1\Part2'],
			'Two_Prefix_Bad' => [false,
			                     new Split('C', ['One', 'Two'], 'A', 'S'),
			                     'XoneStwo'],
			'Many_Empty'     => [true,
			                     new Split('C',
			                               ['A', 'B', 'C', 'D', 'E', 'F', 'G'],
			                               'C|',
			                               '|'),
			                     'C|a|||d|||g'],
			'Prefix_Without_Sep'  => [true,
			                          new Split('Controller',
			                                    ['A', 'B'],
			                                    'Prefix',
			                                    'S'),
			                          'PrefixaSb'],
			'Prefix_WO_Sep_Extra' => [false,
			                          new Split('DC', ['A', 'B'], 'PRE', 'S'),
			                          'PRESb_valueS']
			];
	}

	public function providerParams()
	{
		return [
			'One'      	 => [['One' => 'Part'],
				             new Split('DC', ['One'], 'Cont+', '+'),
				             'Cont+Part'],
			'One_Empty'	 => [['One' => ''],
				             new Split('DC', ['One'], 'Cont+', '+'),
				             'Cont+'],
			'Two' 	   	 => [['P1' => 'Part1', 'P2' => 'Part2'],
					         new Split('DC',
					                   ['P1', 'P2'],
					                   'Controller\\',
					                   '\\'),
					         'Controller\Part1\Part2'],
			'Two_Empty'	 => [['P1' => 'Part1', 'P2' => ''],
				             new Split('DC', ['P1', 'P2'], 'Controller\\', '\\'),
				             'Controller\Part1\\'],
			'Many_Empty' => [['A' => 'a',
			                  'B' => '',
			                  'C' => '',
			                  'D' => 'd',
			                  'E' => '',
			                  'F' => '',
			                  'G' => 'g'],
			                 new Split('C',
			                           ['A', 'B', 'C', 'D', 'E', 'F', 'G'],
			                           'C|',
			                           '|'),
			                 'C|a|||d|||g']];
	}

	/*********/
	/* Tests */
	/*********/

	/**
	 * @dataProvider providerController
	 */
	public function testController($expected, $obj, $uri)
	{
		$obj->setURI($uri);
		$this->assertSame($expected, $obj->getController());
	}

	public function testCreate()
	{
		$obj = new Split(
			'Controller', ['Part_1', 'Part_2'], 'Prefix', 'Separator');
		$this->assertInstanceOf('Evoke\Network\URI\Rule\Split', $obj);
	}

	/**
	 * @expectedException        InvalidArgumentException
	 * @expectedExceptionMessage need parts as non-empty array.
	 */
	public function testCreateInvalidParts()
	{
		$obj = new Split('C', [], 'P', 'S');
	}

	/**
	 * @expectedException        InvalidArgumentException
	 * @expectedExceptionMessage need separator as non-empty string.
	 */
	public function testCreateInvalidSeparator()
	{
		$obj = new Split('Controller', ['Parts'], 'Prefix', '');
	}

	/**
	 * @dataProvider providerMatch
	 */
	public function testMatch($expected, $obj, $uri)
	{
		$obj->setURI($uri);
		$this->assertSame($expected, $obj->isMatch());
	}

	/**
	 * @dataProvider providerParams
	 */
	public function testParams($expected, $obj, $uri)
	{
		$obj->setURI($uri);
		$this->assertSame($expected, $obj->getParams());
	}
}
// EOF