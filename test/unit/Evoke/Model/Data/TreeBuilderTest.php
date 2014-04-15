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
		$rootOnly->set('Root_Only');

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
		$binaryTree->set('Binary_Tree');
		$binaryTree->add($bT0);
		$binaryTree->add($bT1);		
		
		return ['Root_Only' =>
		        ['Tree_Name' => 'Root_Only',
		         'Mptt'      => [['Lft'  => 0,
		                         'Rgt' => 1,
		                         'Value' => 'ROOT_ITEM']],
		         'Expected'  => $rootOnly],
		        'Binary'    =>
		        ['Tree_Name' => 'Binary_Tree',
		         'Mptt'      =>
		         [['Lft' => 0,  'Rgt' => 29, 'V' => 'ROOT_ITEM'],
		          ['Lft' => 1,  'Rgt' => 14, 'V' => '0'],
		          ['Lft' => 2,  'Rgt' => 7,  'V' => '00'],
		          ['Lft' => 3,  'Rgt' => 4,  'V' => '000'],
		          ['Lft' => 5,  'Rgt' => 6,  'V' => '001'],
		          ['Lft' => 8,  'Rgt' => 13, 'V' => '01'],
		          ['Lft' => 9,  'Rgt' => 10, 'V' => '010'],
		          ['Lft' => 11, 'Rgt' => 12, 'V' => '011'],
		          ['Lft' => 15, 'Rgt' => 28, 'V' => '1'],
		          ['Lft' => 16, 'Rgt' => 21, 'V' => '10'],
		          ['Lft' => 17, 'Rgt' => 18, 'V' => '100'],
		          ['Lft' => 19, 'Rgt' => 20, 'V' => '101'],
		          ['Lft' => 22, 'Rgt' => 27, 'V' => '11'],
		          ['Lft' => 23, 'Rgt' => 24, 'V' => '110'],
		          ['Lft' => 25, 'Rgt' => 26, 'V' => '111']],
		         'Expected'  => $binaryTree]];
	}
	
	/*********/
	/* Tests */
	/*********/

	/**
	 * @covers       Evoke\Model\Data\TreeBuilder::build
	 * @dataProvider providerBuild
	 */
	public function testBuild($treeName, $mptt, $expected)
	{
		$obj = new TreeBuilder;

		$this->assertEquals($expected, $obj->build($treeName, $mptt));
	}
	
	/**
	 * @covers Evoke\Model\Data\TreeBuilder::__construct
	 */
	public function testConstruct()
	{
		$obj = new TreeBuilder;
		$this->assertInstanceOf('Evoke\Model\Data\TreeBuilder', $obj);
	}

	/**
	 * @covers                   Evoke\Model\Data\TreeBuilder::build
	 * @expectedException        InvalidArgumentException
	 * @expectedExceptionMessage needs MPTT root with Lft and Rgt fields.
	 */
	public function testInvalidRootNode()
	{
		$obj = new TreeBuilder;
		$obj->build('Tree_Name', [['Root_Node' => 'Bad']]);
	}

	/**
	 * @covers                   Evoke\Model\Data\TreeBuilder::build
	 * @expectedException        InvalidArgumentException
	 * @expectedExceptionMessage needs MPTT entries to build tree.
	 */
	public function testInvalidEmpty()
	{
		$obj = new TreeBuilder;
		$obj->build('Tree_Name', []);
	}

	/**
	 * @covers                   Evoke\Model\Data\TreeBuilder::build
	 * @expectedException        InvalidArgumentException
	 * @expectedExceptionMessage
	 * needs MPTT data at 1 with Lft and Rgt fields.
	 */
	public function testInvalidEntry()
	{
		$obj = new TreeBuilder;
		$obj->build('Tree_Name',
		            [['Lft' => 0, 'Rgt' => 3],
		             ['Lft' => 1, 'Rong' => 2]]);
	}	
}
// EOF