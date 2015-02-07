<?php
namespace Evoke_Test\Model\Mapper;

use Evoke\Model\Mapper\Session;
use PHPUnit_Framework_TestCase;

/**
 * @covers Evoke\Model\Mapper\Session
 * @uses   Evoke\Model\Mapper\MapperIface
 */
class SessionTest extends PHPUnit_Framework_TestCase
{
    /******************/
    /* Data Providers */
    /******************/

    public function providerCreate()
    {
        return [
            'Empty'  => ['Data' => []],
            'Full'   => ['Data' => [1 => 'One', 'Two' => 2, 'Three' => [3]]],
            'Object' => ['Data' => [new \StdClass]]
        ];
    }

    public function providerDelete()
    {
        return [
            'Empty' => ['Data' => []],
            'Full'  => ['Data' => [1 => 'One', 'Two' => 2, 'Three' => [3]]]
        ];
    }

    public function providerRead()
    {
        return [
            'Empty_No_Offset' =>
                [
                    'Offset'    => [],
                    'Read_Data' => []
                ],
            'Empty_Offset'    =>
                [
                    'Offset'    => ['Offset'],
                    'Read_Data' => []
                ],
            'Full'            =>
                [
                    'Offset'    => [],
                    'Read_Data' => ['One', 1, ['Three' => '1 + 1 + 1']]
                ],
            'From_Offset'     =>
                [
                    'Offset'    => ['Two'],
                    'Read_Data' => [1 => 'A', 2 => 'B']
                ],
            'Deep_Offset'     =>
                [
                    'Offset'    => ['A', 2, 'C'],
                    'Read_Data' => [0, 1, 2, 3]
                ],
            'Unset_Offset'    =>
                [
                    'Offset'    => ['B'],
                    'Read_Data' => null
                ]
        ];
    }

    public function providerUpdate()
    {
        return [
            'Simple'  =>
                [
                    'Offset' => ['A', 2],
                    'Data'   => ['Now', 3]
                ],
            'Complex' =>
                [
                    'Offset' => ['A' => ['B' => ['C' => 123]]],
                    'Data'   => ['B' => ['A' => ['C' => '456']]]
                ]
        ];
    }

    /*********/
    /* Tests */
    /*********/

    public function testConstruct()
    {
        $obj = new Session($this->getMock('Evoke\Model\Persistence\SessionIface'));
        $this->assertInstanceOf('Evoke\Model\Mapper\Session', $obj);
    }

    /**
     * @dataProvider providerCreate
     */
    public function testCreate(Array $data)
    {
        $mockSession = $this->getMock('Evoke\Model\Persistence\SessionIface');
        $mockSession
            ->expects($this->once())
            ->method('setData')
            ->with($data);

        $obj = new Session($mockSession);
        $obj->create($data);
    }

    /**
     * @dataProvider providerDelete
     */
    public function testDelete($offset)
    {
        $mockSession = $this->getMock('Evoke\Model\Persistence\SessionIface');
        $mockSession
            ->expects($this->once())
            ->method('deleteAtOffset')
            ->with($offset);

        $obj = new Session($mockSession);
        $obj->delete($offset);
    }

    /**
     * @dataProvider providerRead
     */
    public function testRead($offset, $readData)
    {
        $mockSession = $this->getMock('Evoke\Model\Persistence\SessionIface');
        $mockSession
            ->expects($this->once())
            ->method('getAtOffset')
            ->with($offset)
            ->will($this->returnValue($readData));

        $obj = new Session($mockSession);
        $this->assertSame($readData, $obj->read($offset));
    }

    /**
     * @dataProvider providerUpdate
     */
    public function testUpdate(Array $offset, Array $data)
    {
        $mockSession = $this->getMock('Evoke\Model\Persistence\SessionIface');
        $mockSession
            ->expects($this->once())
            ->method('setDataAtOffset')
            ->with($data, $offset);

        $obj = new Session($mockSession);
        $obj->update($offset, $data);
    }
}
// EOF
