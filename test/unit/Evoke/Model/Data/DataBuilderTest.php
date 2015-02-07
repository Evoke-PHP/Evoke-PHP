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
            ->will($this->returnValue(['Leaf_1' => $leaf1]));
        $branch2 = $this->getMock('Evoke\Model\Data\Join\JoinIface');
        $branch2
            ->expects($this->any())
            ->method('getJoins')
            ->with()
            ->will($this->returnValue(['Leaf_2' => $leaf2]));

        $branchAll = $this->getMock('Evoke\Model\Data\Join\JoinIface');
        $branchAll
            ->expects($this->any())
            ->method('getJoins')
            ->with()
            ->will($this->returnValue([
                'Leaf_1' => $leaf1,
                'Leaf_2' => $leaf2
            ]));
        $trunk = $this->getMock('Evoke\Model\Data\Join\JoinIface');
        $trunk
            ->expects($this->any())
            ->method('getJoins')
            ->with()
            ->will($this->returnValue([
                'Branch_1'   => $branch1,
                'Branch_2'   => $branch2,
                'Branch_All' => $branchAll
            ]));

        // Set up the expected data
        $dataLeaf1     = new Data($leaf1);
        $dataLeaf2     = new Data($leaf2);
        $dataBranch1   = new Data($branch1, ['Leaf_1' => $dataLeaf1]);
        $dataBranch2   = new Data($branch2, ['Leaf_2' => $dataLeaf2]);
        $dataBranchAll = new Data($branchAll, [
            'Leaf_1' => $dataLeaf1,
            'Leaf_2' => $dataLeaf2
        ]);
        $dataTrunk     = new Data($trunk, [
            'Branch_1'   => $dataBranch1,
            'Branch_2'   => $dataBranch2,
            'Branch_All' => $dataBranchAll
        ]);

        return [
            'Branch_All' => [
                'Expected'       => $dataBranchAll,
                'Join_Structure' => $branchAll
            ],
            'Branch_1'   => [
                'Expected'       => $dataBranch1,
                'Join_Structure' => $branch1
            ],
            'Branch_2'   => [
                'Expected'       => $dataBranch2,
                'Join_Structure' => $branch2
            ],
            'Leaf_1'     => [
                'Expected'       => $dataLeaf1,
                'Join_Structure' => $leaf1
            ],
            'Leaf_2'     => [
                'Expected'       => $dataLeaf2,
                'Join_Structure' => $leaf2
            ],
            'Trunk'      => [
                'Expected'       => $dataTrunk,
                'Join_Structure' => $trunk
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
