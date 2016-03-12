<?php
namespace Evoke_Test\Model\Data;

use Evoke\Model\Data\Data;
use Evoke\Model\Data\DataBuilder;
use Evoke\Model\Data\Join\JoinIface;
use PHPUnit_Framework_TestCase;

/**
 * @covers Evoke\Model\Data\DataBuilder
 * @uses   Evoke\Model\Data\Data::__construct
 */
class DataBuilderTest extends PHPUnit_Framework_TestCase
{
    /******************/
    /* Data Providers */
    /******************/

    public function providerBuild()
    {
        // Set up the joins.
        $leaf1 = $this->getMock('Evoke\Model\Data\Join\JoinIface');
        $leaf1
            ->expects($this->any())
            ->method('getJoins')
            ->with()
            ->will($this->returnValue([]));
        $leaf2 = $this->getMock('Evoke\Model\Data\Join\JoinIface');
        $leaf2
            ->expects($this->any())
            ->method('getJoins')
            ->with()
            ->will($this->returnValue([]));

        $branch1 = $this->getMock('Evoke\Model\Data\Join\JoinIface');
        $branch1
            ->expects($this->any())
            ->method('getJoins')
            ->with()
            ->will($this->returnValue(['leaf_1' => $leaf1]));
        $branch2 = $this->getMock('Evoke\Model\Data\Join\JoinIface');
        $branch2
            ->expects($this->any())
            ->method('getJoins')
            ->with()
            ->will($this->returnValue(['leaf_2' => $leaf2]));

        $branchAll = $this->getMock('Evoke\Model\Data\Join\JoinIface');
        $branchAll
            ->expects($this->any())
            ->method('getJoins')
            ->with()
            ->will($this->returnValue([
                'leaf_1' => $leaf1,
                'leaf_2' => $leaf2
            ]));

        $trunk = $this->getMock('Evoke\Model\Data\Join\JoinIface');
        $trunk
            ->expects($this->any())
            ->method('getJoins')
            ->with()
            ->will($this->returnValue([
                'branch_1'   => clone $branch1,
                'branch_2'   => clone $branch2,
                'branch_all' => clone $branchAll
            ]));

        // Set up the expected data
        $dataLeaf1     = new Data($leaf1);
        $dataLeaf2     = new Data($leaf2);
        $dataBranch1   = new Data($branch1, ['leaf_1' => $dataLeaf1]);
        $dataBranch2   = new Data($branch2, ['leaf_2' => $dataLeaf2]);
        $dataBranchAll = new Data($branchAll, [
            'leaf_1' => $dataLeaf1,
            'leaf_2' => $dataLeaf2
        ]);
        $dataTrunk     = new Data($trunk, [
            'branch_1'   => $dataBranch1,
            'branch_2'   => $dataBranch2,
            'branch_all' => $dataBranchAll
        ]);

        return [
            'branch_all' => [
                'expected'       => $dataBranchAll,
                'join_structure' => $branchAll
            ],
            'branch_1'   => [
                'expected'       => $dataBranch1,
                'join_structure' => $branch1
            ],
            'branch_2'   => [
                'expected'       => $dataBranch2,
                'join_structure' => $branch2
            ],
            'leaf_1'     => [
                'expected'       => $dataLeaf1,
                'join_structure' => $leaf1
            ],
            'leaf_2'     => [
                'expected'       => $dataLeaf2,
                'join_structure' => $leaf2
            ],
            'trunk'      => [
                'expected'       => $dataTrunk,
                'join_structure' => $trunk
            ]
        ];
    }

    /*********/
    /* Tests */
    /*********/

    /**
     * @dataProvider providerBuild
     */
    public function testBuild(Data $expected, JoinIface $joinStructure)
    {
        $obj = new DataBuilder;
        $this->assertEquals($expected, $obj->build($joinStructure));
    }
}
// EOF
