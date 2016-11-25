<?php
namespace Evoke_Test\Model\Data;

use Evoke\Model\Data\Tree;
use Evoke\Model\Data\TreeBuilder;
use PHPUnit_Framework_TestCase;

/**
 * @covers Evoke\Model\Data\TreeBuilder
 * @uses   Evoke\Model\Data\Tree
 */
class TreeBuilderTest extends PHPUnit_Framework_TestCase
{
    /******************/
    /* Data Providers */
    /******************/

    public function providerBuild()
    {
        $rootOnly = new Tree;
        $rootOnly->set('root_only');

        $bTItems = [
            '0',
            '00',
            '000',
            '001',
            '01',
            '010',
            '011',
            '1',
            '10',
            '100',
            '101',
            '11',
            '110',
            '111'
        ];

        foreach ($bTItems as $item) {
            $treeItem  = 'bT' . $item;
            $$treeItem = new Tree;
            $$treeItem->set(['v' => $item]);
        }

        foreach ($bTItems as $item) {
            if (strlen($item) < 3) {
                $treeItem  = 'bT' . $item;
                $leftItem  = $treeItem . '0';
                $rightItem = $treeItem . '1';
                $$treeItem->add($$leftItem);
                $$treeItem->add($$rightItem);
            }
        }

        $binaryTree = new Tree;
        $binaryTree->set('binary_tree');
        $binaryTree->add($bT0);
        $binaryTree->add($bT1);

        return [
            'root_only' =>
                [
                    'tree_name' => 'root_only',
                    'mptt'      => [
                        [
                            'lft'   => 0,
                            'rgt'   => 1,
                            'value' => 'ROOT_ITEM'
                        ]
                    ],
                    'expected'  => $rootOnly
                ],
            'binary'    =>
                [
                    'tree_name' => 'binary_tree',
                    'mptt'      =>
                        [
                            ['lft' => 0,  'rgt' => 29, 'v' => 'ROOT_ITEM'],
                            ['lft' => 1,  'rgt' => 14, 'v' => '0'],
                            ['lft' => 2,  'rgt' => 7,  'v' => '00'],
                            ['lft' => 3,  'rgt' => 4,  'v' => '000'],
                            ['lft' => 5,  'rgt' => 6,  'v' => '001'],
                            ['lft' => 8,  'rgt' => 13, 'v' => '01'],
                            ['lft' => 9,  'rgt' => 10, 'v' => '010'],
                            ['lft' => 11, 'rgt' => 12, 'v' => '011'],
                            ['lft' => 15, 'rgt' => 28, 'v' => '1'],
                            ['lft' => 16, 'rgt' => 21, 'v' => '10'],
                            ['lft' => 17, 'rgt' => 18, 'v' => '100'],
                            ['lft' => 19, 'rgt' => 20, 'v' => '101'],
                            ['lft' => 22, 'rgt' => 27, 'v' => '11'],
                            ['lft' => 23, 'rgt' => 24, 'v' => '110'],
                            ['lft' => 25, 'rgt' => 26, 'v' => '111']
                        ],
                    'expected'  => $binaryTree
                ]
        ];
    }

    /*********/
    /* Tests */
    /*********/

    /**
     * @dataProvider providerBuild
     */
    public function testBuild($treeName, $mptt, $expected)
    {
        $obj = new TreeBuilder;

        $this->assertEquals($expected, $obj->build($treeName, $mptt));
    }

    public function testConstruct()
    {
        $obj = new TreeBuilder;
        $this->assertInstanceOf('Evoke\Model\Data\TreeBuilder', $obj);
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage needs MPTT root with lft and rgt fields.
     */
    public function testInvalidRootNode()
    {
        $obj = new TreeBuilder;
        $obj->build('tree_name', [['root_node' => 'bad']]);
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage needs MPTT entries to build tree.
     */
    public function testInvalidEmpty()
    {
        $obj = new TreeBuilder;
        $obj->build('tree_name', []);
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage
     * needs MPTT data at 1 with lft and rgt fields.
     */
    public function testInvalidEntry()
    {
        $obj = new TreeBuilder;
        $obj->build(
            'tree_name',
            [
                ['lft' => 0, 'rgt' => 3],
                ['lft' => 1, 'Rong' => 2]
            ]
        );
    }
}
// EOF
