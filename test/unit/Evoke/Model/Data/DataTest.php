<?php
namespace Evoke_Test\Model\Data;

use Evoke\Model\Data\Data;
use PHPUnit_Framework_TestCase;

/**
 * @covers Evoke\Model\Data\Data
 * @uses   Evoke\Model\Data\Flat
 */
class DataTest extends PHPUnit_Framework_TestCase
{
    /*******************/
    /* Private Methods */
    /*******************/

    public function providerCreate()
    {
        $join = $this->getMock('Evoke\Model\Data\Join\JoinIface');
        $data = $this->getDataMock();

        return [
            'Simple'          => [$join],
            'Two_Specified'   => [$join, ['J' => $data]],
            'Fully_Specified' => [$join, ['J' => $data], 'Join_Key']
        ];
    }

    /******************/
    /* Data Providers */
    /******************/

    public function providerGetJointData()
    {
        $simpleJoin = $this->getMock('Evoke\Model\Data\Join\JoinIface');
        $simpleJoin
            ->expects($this->once())
            ->method('getJoinID')
            ->with('Join_Name')
            ->will($this->returnValue('Found_Join_ID'));
        $simpleData = $this->getDataMock();

        return [
            'Simple' =>
                [
                    'Join'      => $simpleJoin,
                    'Joins'     =>
                        [
                            'Found_Join_ID' => $simpleData,
                            'DC'            => $this->getMock('Evoke\Model\Data\Join\JoinIface')
                        ],
                    'Join_Name' => 'Join_Name',
                    'Expected'  => $simpleData
                ]
        ];
    }

    /**
     * @dataProvider providerCreate
     */
    public function testCreate()
    {
        $args = func_get_args();

        switch (count($args)) {
            case 1:
                $obj = new Data($args[0]);
                break;
            case 2:
                $obj = new Data($args[0], $args[1]);
                break;
            case 3:
                $obj = new Data($args[0], $args[1], $args[2]);
                break;
            default:
                throw new \RuntimeException('Test failed due to unexpected number of arguments.');
        }

        $this->assertInstanceOf('Evoke\Model\Data\Data', $obj);
    }

    /*********/
    /* Tests */
    /*********/

    /**
     * @dataProvider providerGetJointData
     */
    public function testGetJointData($join, $joins, $joinName, $expected)
    {
        $obj = new Data($join, $joins);
        $this->assertSame($expected, $obj->$joinName);
    }

    /**
     * @expectedException        OutOfBoundsException
     * @expectedExceptionMessage no data container for join: join
     */
    public function testGetJointDataException()
    {
        $join = $this->getMock('Evoke\Model\Data\Join\JoinIface');
        $join
            ->expects($this->once())
            ->method('getJoinID')
            ->with('join')
            ->will($this->returnValue('NoMatch'));

        $obj = new Data($join);
        $obj->join;
    }

    public function testSetArrangedData()
    {
        $joinObjectUnderTest = $this->getMock('Evoke\Model\Data\Join\JoinIface');
        $joinObjectUnderTest
            ->expects($this->never())
            ->method('arrangeFlatData');

        $flatResults  = [['Dont_Care' => 'Mock Arranges This']];
        $arrangedData = [
            [
                'Any'        => 'OK',
                'Joint_Data' => ['J1' => [['F1' => 'Arranged']]]
            ]
        ];
        $joinOuter    = $this->getMock('Evoke\Model\Data\Join\JoinIface');
        $joinOuter
            ->expects($this->once())
            ->method('arrangeFlatData')
            ->with($flatResults)
            ->will($this->returnValue($arrangedData));

        $objectUnderTest = new Data($joinObjectUnderTest);
        $outer           = new Data(
            $joinOuter,
            ['J1' => $objectUnderTest]
        );
        $outer->setData($flatResults);
    }

    public function testSetData()
    {
        $j1Data1 = [
            ['J1_ID' => 1, 'Value' => '1'],
            ['J1_ID' => 1, 'Value' => 'One']
        ];
        $j1Data2 = [
            ['J1_ID' => 2, 'Value' => '12'],
            ['J1_ID' => 2, 'Value' => 'OneTwo']
        ];

        $j2Data1 = [
            ['J2_ID' => 1, 'Value' => '21'],
            ['J2_ID' => 1, 'Value' => 'TwoOne']
        ];
        $j2Data2 = [
            ['J2_ID' => 2, 'Value' => '2'],
            ['J2_ID' => 2, 'Value' => 'Two']
        ];
        $j3Data  = [['J3_ID' => 3, 'Text' => 'Three']];

        $flatResults =
            [
                [
                    'M.Main_Record' => 'One',
                    'J1.J1_ID'      => 1,
                    'J1.Value'      => '1',
                    'J2.J2_ID'      => 1,
                    'J2.Value'      => '21'
                ],
                [
                    'M.Main_Record' => 'One',
                    'J1.J1_ID'      => 1,
                    'J1.Value'      => 'One',
                    'J2.J2_ID'      => 1,
                    'J2.Value'      => '21'
                ],
                [
                    'M.Main_Record' => 'One',
                    'J1.J1_ID'      => 1,
                    'J1.Value'      => '1',
                    'J2.J2_ID'      => 1,
                    'J2.Value'      => 'TwoOne',
                    'J3.J3_ID'      => 3,
                    'J3.Text'       => 'Three'
                ],
                [
                    'M.Main_Record' => 'One',
                    'J1.J1_ID'      => 1,
                    'J1.Value'      => '1',
                    'J2.J2_ID'      => 1,
                    'J2.Value'      => 'TwoOne',
                    'J3.J3_ID'      => 3,
                    'J3.Text'       => 'Three'
                ]
            ];

        $data = [
            [
                'Main_Record' => 'One',
                'Joint_Data'  => [
                    'J1' => $j1Data1,
                    'J2' => $j2Data1
                ]
            ],
            [
                'Main_Record' => 'Two',
                'Joint_Data'  => [
                    'J1' => $j1Data2,
                    'J2' => $j2Data2,
                    'J3' => $j3Data
                ]
            ]
        ];

        $j1 = $this->getDataMock();
        $j1
            ->expects($this->at(0))
            ->method('setArrangedData')
            ->with($j1Data1);
        $j1
            ->expects($this->at(1))
            ->method('setArrangedData')
            ->with($j1Data2);

        $j2 = $this->getDataMock();
        $j2
            ->expects($this->at(0))
            ->method('setArrangedData')
            ->with($j2Data1);
        $j2
            ->expects($this->at(1))
            ->method('setArrangedData')
            ->with($j2Data2);

        $j3 = $this->getDataMock();
        $j3
            ->expects($this->at(0))
            ->method('setArrangedData')
            ->with([]);
        $j3
            ->expects($this->at(1))
            ->method('setArrangedData')
            ->with($j3Data);

        $join = $this->getMock('Evoke\Model\Data\Join\JoinIface');
        $join
            ->expects($this->at(0))
            ->method('arrangeFlatData')
            ->with($flatResults)
            ->will($this->returnValue($data));

        $obj = new Data(
            $join,
            ['J1' => $j1, 'J2' => $j2, 'J3' => $j3]
        );
        $obj->setData($flatResults);
        $obj->next();
    }

    private function getDataMock()
    {
        return $this->getMockBuilder('Evoke\Model\Data\Data')
            ->disableOriginalConstructor()
            ->setMethods(['setData', 'setArrangedData'])
            ->getMock();
    }
}
// EOF
