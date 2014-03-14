<?php
namespace Evoke_Test\Model\Data;

use Evoke\Model\Data\TreeBuilder,
	Evoke\Model\Data\Tree,
	PHPUnit_Framework_TestCase;

class TreeBuilderTest extends PHPUnit_Framework_TestCase
{
	/******************/
	/* Data Providers */
	/******************/

	public function providerBuild()
	{
		$rootOnly = new Tree;
		$rootOnly->set(['Value' => 'V']);

		$bTItems = ['0', '00', '000', '001', '01', '010', '011',
		            '1', '10', '100', '101', '11', '110', '111'];
		
		foreach ($bTItems as $item)
		{
			$treeItem = 'bT' . $item;
			$$treeItem = new Tree;
			$$treeItem->set(['V' => $item]);
		}

		foreach ($bTItems as $item)
		{
			if (strlen($item) < 3)
			{
				$treeItem  = 'bT' . $item;
				$leftItem  = $treeItem . '0';
				$rightItem = $treeItem . '1';
				$$treeItem->add($$leftItem);
				$$treeItem->add($$rightItem);
			}
		}
		
		$binaryTree = new Tree;
		$binaryTree->set(['V' => 'Root_Node']);
		$binaryTree->add($bT0);
		$binaryTree->add($bT1);		
		
		return ['Root_Only' =>
		        ['Mptt'     => [['Left'  => 0,
		                         'Right' => 1,
		                         'Value' => 'V']],
		         'Expected' => $rootOnly],
		        'Binary'    =>
		        ['Mptt'     =>
		         [['Left' => 0,  'Right' => 29, 'V' => 'Root_Node'],
		          ['Left' => 1,  'Right' => 14, 'V' => '0'],
		          ['Left' => 2,  'Right' => 7,  'V' => '00'],
		          ['Left' => 3,  'Right' => 4,  'V' => '000'],
		          ['Left' => 5,  'Right' => 6,  'V' => '001'],
		          ['Left' => 8,  'Right' => 13, 'V' => '01'],
		          ['Left' => 9,  'Right' => 10, 'V' => '010'],
		          ['Left' => 11, 'Right' => 12, 'V' => '011'],
		          ['Left' => 15, 'Right' => 28, 'V' => '1'],
		          ['Left' => 16, 'Right' => 21, 'V' => '10'],
		          ['Left' => 17, 'Right' => 18, 'V' => '100'],
		          ['Left' => 19, 'Right' => 20, 'V' => '101'],
		          ['Left' => 22, 'Right' => 27, 'V' => '11'],
		          ['Left' => 23, 'Right' => 24, 'V' => '110'],
		          ['Left' => 25, 'Right' => 26, 'V' => '111']],
		         'Expected' => $binaryTree]];
	}
	
	/*********/
	/* Tests */
	/*********/

	/**
	 * Construct an object.
	 *
	 * @covers Evoke\Model\Data\TreeBuilder::__construct
	 */
	public function testConstruct()
	{
		$obj = new TreeBuilder;
		$this->assertInstanceOf('Evoke\Model\Data\TreeBuilder', $obj);
	}

	/**
	 *
	 *
	 * @covers       Evoke\Model\Data\TreeBuilder::build
	 * @dataProvider providerBuild
	 */
	public function testBuild($mptt, $expected)
	{
		$obj = new TreeBuilder;

		$this->assertEquals($expected, $obj->build($mptt));
	}
}
// EOF