<?php
namespace Evoke_Test\Model\Data;

use Evoke\Model\Data\Decorator,
    PHPUnit_Framework_TestCase;

// We are testing an abstract class.
class Test_Extended_Decorator extends Decorator
{
}

class DecoratorTest extends PHPUnit_Framework_TestCase
{
    /*********/
    /* Tests */
    /*********/

    /**
     * @covers Evoke\Model\Data\Decorator::__construct
     */
    public function testCreate()
    {
        $obj = new Test_Extended_Decorator(
            $this->getMock('Evoke\Model\Data\FlatIface'));
        $this->assertInstanceOf('Evoke\Model\Data\Decorator', $obj);
    }

    /**
     * @covers Evoke\Model\Data\Decorator::__construct
     * @covers Evoke\Model\Data\Decorator::current
     */
    public function testCurrent()
    {
        $dataMock = $this->getMock('Evoke\Model\Data\FlatIface');
        $dataMock
            ->expects($this->once())
            ->method('current')
            ->with()
            ->will($this->returnValue($dataMock));

        $obj = new Test_Extended_Decorator($dataMock);
        $this->assertSame($dataMock, $obj->current());
    }

    /**
     * @covers Evoke\Model\Data\Decorator::__construct
     * @covers Evoke\Model\Data\Decorator::getRecord
     */
    public function testGetRecord()
    {
        $expected = ['ID' => 1, 'Value' => 'Current'];
        $dataMock = $this->getMock('Evoke\Model\Data\FlatIface');
        $dataMock
            ->expects($this->once())
            ->method('getRecord')
            ->with()
            ->will($this->returnValue($expected));

        $obj = new Test_Extended_Decorator($dataMock);
        $this->assertSame($expected, $obj->getRecord());
    }

    /**
     * @covers Evoke\Model\Data\Decorator::__construct
     * @covers Evoke\Model\Data\Decorator::isEmpty
     */
    public function testIsEmpty()
    {
        $expected = true;
        $dataMock = $this->getMock('Evoke\Model\Data\FlatIface');
        $dataMock
            ->expects($this->once())
            ->method('isEmpty')
            ->with()
            ->will($this->returnValue($expected));

        $obj = new Test_Extended_Decorator($dataMock);
        $this->assertSame($expected, $obj->isEmpty());
    }

    /**
     * @covers Evoke\Model\Data\Decorator::__construct
     * @covers Evoke\Model\Data\Decorator::key
     */
    public function testKey()
    {
        $expected = 'Key_ID';
        $dataMock = $this->getMock('Evoke\Model\Data\FlatIface');
        $dataMock
            ->expects($this->once())
            ->method('key')
            ->with()
            ->will($this->returnValue($expected));

        $obj = new Test_Extended_Decorator($dataMock);
        $this->assertSame($expected, $obj->key());
    }

    /**
     * @covers Evoke\Model\Data\Decorator::__construct
     * @covers Evoke\Model\Data\Decorator::next
     */
    public function testNext()
    {
        $expected = false;
        $dataMock = $this->getMock('Evoke\Model\Data\FlatIface');
        $dataMock
            ->expects($this->once())
            ->method('next')
            ->with()
            ->will($this->returnValue($expected));

        $obj = new Test_Extended_Decorator($dataMock);
        $this->assertSame($expected, $obj->next());
    }

    /**
     * @covers Evoke\Model\Data\Decorator::__construct
     * @covers Evoke\Model\Data\Decorator::offsetExists
     */
    public function testOffsetExists()
    {
        $expected = true;
        $dataMock = $this->getMock('Evoke\Model\Data\FlatIface');
        $dataMock
            ->expects($this->once())
            ->method('offsetExists')
            ->with('Offset_To_Check')
            ->will($this->returnValue($expected));

        $obj = new Test_Extended_Decorator($dataMock);
        $this->assertSame($expected, isset($obj['Offset_To_Check']));
    }

    /**
     * @covers Evoke\Model\Data\Decorator::__construct
     * @covers Evoke\Model\Data\Decorator::offsetGet
     */
    public function testOffsetGet()
    {
        $expected = 'Value';
        $dataMock = $this->getMock('Evoke\Model\Data\FlatIface');
        $dataMock
            ->expects($this->once())
            ->method('offsetGet')
            ->with('Offset_Desired')
            ->will($this->returnValue($expected));

        $obj = new Test_Extended_Decorator($dataMock);
        $this->assertSame($expected, $obj['Offset_Desired']);
    }

    /**
     * @covers Evoke\Model\Data\Decorator::__construct
     * @covers Evoke\Model\Data\Decorator::offsetSet
     */
    public function testOffsetSet()
    {
        $expected = 'Value';
        $dataMock = $this->getMock('Evoke\Model\Data\FlatIface');
        $dataMock
            ->expects($this->once())
            ->method('offsetSet')
            ->with('Offset_Desired', $expected);

        $obj = new Test_Extended_Decorator($dataMock);
        $obj['Offset_Desired'] = $expected;
    }

    /**
     * @covers Evoke\Model\Data\Decorator::__construct
     * @covers Evoke\Model\Data\Decorator::offsetUnset
     */
    public function testOffsetUnset()
    {
        $dataMock = $this->getMock('Evoke\Model\Data\FlatIface');
        $dataMock
            ->expects($this->once())
            ->method('offsetUnset')
            ->with('Offset_Desired');

        $obj = new Test_Extended_Decorator($dataMock);
        unset($obj['Offset_Desired']);
    }

    /**
     * @covers Evoke\Model\Data\Decorator::__construct
     * @covers Evoke\Model\Data\Decorator::rewind
     */
    public function testRewind()
    {
        $dataMock = $this->getMock('Evoke\Model\Data\FlatIface');
        $dataMock
            ->expects($this->once())
            ->method('rewind')
            ->with();

        $obj = new Test_Extended_Decorator($dataMock);
        $obj->rewind();
    }

    /**
     * @covers Evoke\Model\Data\Decorator::__construct
     * @covers Evoke\Model\Data\Decorator::setData
     */
    public function testSetData()
    {
        $expected = [['ID' => 1, 'V' => 'SD1'],
                     ['ID' => 2, 'V' => 'SD2']];
        $dataMock = $this->getMock('Evoke\Model\Data\FlatIface');
        $dataMock
            ->expects($this->once())
            ->method('setData')
            ->with($expected);

        $obj = new Test_Extended_Decorator($dataMock);
        $obj->setData($expected);
    }

    /**
     * @covers Evoke\Model\Data\Decorator::__construct
     * @covers Evoke\Model\Data\Decorator::valid
     */
    public function testValid()
    {
        $expected = true;
        $dataMock = $this->getMock('Evoke\Model\Data\FlatIface');
        $dataMock
            ->expects($this->once())
            ->method('valid')
            ->with()
            ->will($this->returnValue($expected));

        $obj = new Test_Extended_Decorator($dataMock);
        $this->assertSame($expected, $obj->valid());
    }
}
// EOF