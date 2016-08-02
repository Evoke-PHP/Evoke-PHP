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
            'simple'          => [$join],
            'two_specified'   => [$join, ['j' => $data]],
            'fully_specified' => [$join, ['j' => $data], 'join_key']
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
            ->with('join_name')
            ->will($this->returnValue('found_join_id'));
        $simpleData = $this->getDataMock();

        return [
            'simple' =>
                [
                    'join'      => $simpleJoin,
                    'joins'     =>
                        [
                            'found_join_id' => $simpleData,
                            'dc'            => $this->getMock('Evoke\Model\Data\Join\JoinIface')
                        ],
                    'join_name' => 'join_name',
                    'expected'  => $simpleData
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
     * @covers Evoke\Model\Data\Flat:count
     */
    public function testCount()
    {
        $j1Data1 = [
            ['j1_id' => 1, 'value' => '1'],
            ['j1_id' => 1, 'value' => 'one']
        ];
        $j1Data2 = [
            ['j1_id' => 2, 'value' => '12'],
            ['j1_id' => 2, 'value' => 'onetwo']
        ];

        $j2Data1 = [
            ['j2_id' => 1, 'value' => '21'],
            ['j2_id' => 1, 'value' => 'twoone']
        ];
        $j2Data2 = [
            ['j2_id' => 2, 'value' => '2'],
            ['j2_id' => 2, 'value' => 'two']
        ];
        $j3Data  = [['j3_id' => 3, 'text' => 'three']];

        $flatResults =
            [
                [
                    'm.main_record' => 'one',
                    'j1.j1_id'      => 1,
                    'j1.value'      => '1',
                    'j2.j2_id'      => 1,
                    'j2.value'      => '21'
                ],
                [
                    'm.main_record' => 'one',
                    'j1.j1_id'      => 1,
                    'j1.value'      => 'one',
                    'j2.j2_id'      => 1,
                    'j2.value'      => '21'
                ],
                [
                    'm.main_record' => 'one',
                    'j1.j1_id'      => 1,
                    'j1.value'      => '1',
                    'j2.j2_id'      => 1,
                    'j2.value'      => 'twoone',
                    'j3.j3_id'      => 3,
                    'j3.text'       => 'three'
                ],
                [
                    'm.main_record' => 'one',
                    'j1.j1_id'      => 1,
                    'j1.value'      => '1',
                    'j2.j2_id'      => 1,
                    'j2.value'      => 'twoone',
                    'j3.j3_id'      => 3,
                    'j3.text'       => 'three'
                ]
            ];

        $data = [
            [
                'main_record' => 'one',
                'joint_data'  => [
                    'j1' => $j1Data1,
                    'j2' => $j2Data1
                ]
            ],
            [
                'main_record' => 'two',
                'joint_data'  => [
                    'j1' => $j1Data2,
                    'j2' => $j2Data2,
                    'j3' => $j3Data
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
            ['j1' => $j1, 'j2' => $j2, 'j3' => $j3]
        );
        $obj->setData($flatResults);
        $obj->next();

        $this->assertEquals(2, $obj->count());
    }

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

        $flatResults  = [['dont_care' => 'Mock Arranges This']];
        $arrangedData = [
            [
                'any'        => 'ok',
                'joint_data' => ['j1' => [['f1' => 'arranged']]]
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
            ['j1' => $objectUnderTest]
        );
        $outer->setData($flatResults);
    }

    public function testSetData()
    {
        $j1Data1 = [
            ['j1_id' => 1, 'value' => '1'],
            ['j1_id' => 1, 'value' => 'one']
        ];
        $j1Data2 = [
            ['j1_id' => 2, 'value' => '12'],
            ['j1_id' => 2, 'value' => 'onetwo']
        ];

        $j2Data1 = [
            ['j2_id' => 1, 'value' => '21'],
            ['j2_id' => 1, 'value' => 'twoone']
        ];
        $j2Data2 = [
            ['j2_id' => 2, 'value' => '2'],
            ['j2_id' => 2, 'value' => 'two']
        ];
        $j3Data  = [['j3_id' => 3, 'text' => 'three']];

        $flatResults =
            [
                [
                    'm.main_record' => 'one',
                    'j1.j1_id'      => 1,
                    'j1.value'      => '1',
                    'j2.j2_id'      => 1,
                    'j2.value'      => '21'
                ],
                [
                    'm.main_record' => 'one',
                    'j1.j1_id'      => 1,
                    'j1.value'      => 'one',
                    'j2.j2_id'      => 1,
                    'j2.value'      => '21'
                ],
                [
                    'm.main_record' => 'one',
                    'j1.j1_id'      => 1,
                    'j1.value'      => '1',
                    'j2.j2_id'      => 1,
                    'j2.value'      => 'twoone',
                    'j3.j3_id'      => 3,
                    'j3.text'       => 'three'
                ],
                [
                    'm.main_record' => 'one',
                    'j1.j1_id'      => 1,
                    'j1.value'      => '1',
                    'j2.j2_id'      => 1,
                    'j2.value'      => 'twoone',
                    'j3.j3_id'      => 3,
                    'j3.text'       => 'three'
                ]
            ];

        $data = [
            [
                'main_record' => 'one',
                'joint_data'  => [
                    'j1' => $j1Data1,
                    'j2' => $j2Data1
                ]
            ],
            [
                'main_record' => 'two',
                'joint_data'  => [
                    'j1' => $j1Data2,
                    'j2' => $j2Data2,
                    'j3' => $j3Data
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
            ['j1' => $j1, 'j2' => $j2, 'j3' => $j3]
        );
        $obj->setData($flatResults);
        $obj->next();
    }

    /*******************/
    /* Private Methods */
    /*******************/

    private function getDataMock()
    {
        return $this->getMockBuilder('Evoke\Model\Data\Data')
            ->disableOriginalConstructor()
            ->setMethods(['setData', 'setArrangedData'])
            ->getMock();
    }
}
// EOF
