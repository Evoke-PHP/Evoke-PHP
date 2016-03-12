<?php
namespace Evoke_Test\Model\Data;

use Evoke\Model\Data\Decorator;
use PHPUnit_Framework_TestCase;

// We are testing an abstract class.
class TestExtendedDecorator extends Decorator
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
        $obj = new TestExtendedDecorator($this->getMock('Evoke\Model\Data\FlatIface'));
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

        $obj = new TestExtendedDecorator($dataMock);
        $this->assertSame($dataMock, $obj->current());
    }

    /**
     * @covers Evoke\Model\Data\Decorator::__construct
     * @covers Evoke\Model\Data\Decorator::getRecord
     */
    public function testGetRecord()
    {
        $expected = ['id' => 1, 'value' => 'current'];
        $dataMock = $this->getMock('Evoke\Model\Data\FlatIface');
        $dataMock
            ->expects($this->once())
            ->method('getRecord')
            ->with()
            ->will($this->returnValue($expected));

        $obj = new TestExtendedDecorator($dataMock);
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

        $obj = new TestExtendedDecorator($dataMock);
        $this->assertSame($expected, $obj->isEmpty());
    }

    /**
     * @covers Evoke\Model\Data\Decorator::__construct
     * @covers Evoke\Model\Data\Decorator::key
     */
    public function testKey()
    {
        $expected = 'key_id';
        $dataMock = $this->getMock('Evoke\Model\Data\FlatIface');
        $dataMock
            ->expects($this->once())
            ->method('key')
            ->with()
            ->will($this->returnValue($expected));

        $obj = new TestExtendedDecorator($dataMock);
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

        $obj = new TestExtendedDecorator($dataMock);
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
            ->with('offset_to_check')
            ->will($this->returnValue($expected));

        $obj = new TestExtendedDecorator($dataMock);
        $this->assertSame($expected, isset($obj['offset_to_check']));
    }

    /**
     * @covers Evoke\Model\Data\Decorator::__construct
     * @covers Evoke\Model\Data\Decorator::offsetGet
     */
    public function testOffsetGet()
    {
        $expected = 'value';
        $dataMock = $this->getMock('Evoke\Model\Data\FlatIface');
        $dataMock
            ->expects($this->once())
            ->method('offsetGet')
            ->with('offset_desired')
            ->will($this->returnValue($expected));

        $obj = new TestExtendedDecorator($dataMock);
        $this->assertSame($expected, $obj['offset_desired']);
    }

    /**
     * @covers Evoke\Model\Data\Decorator::__construct
     * @covers Evoke\Model\Data\Decorator::offsetSet
     */
    public function testOffsetSet()
    {
        $expected = 'value';
        $dataMock = $this->getMock('Evoke\Model\Data\FlatIface');
        $dataMock
            ->expects($this->once())
            ->method('offsetSet')
            ->with('offset_desired', $expected);

        $obj                   = new TestExtendedDecorator($dataMock);
        $obj['offset_desired'] = $expected;
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
            ->with('offset_desired');

        $obj = new TestExtendedDecorator($dataMock);
        unset($obj['offset_desired']);
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

        $obj = new TestExtendedDecorator($dataMock);
        $obj->rewind();
    }

    /**
     * @covers Evoke\Model\Data\Decorator::__construct
     * @covers Evoke\Model\Data\Decorator::setData
     */
    public function testSetData()
    {
        $expected = [
            ['id' => 1, 'v' => 'sd1'],
            ['id' => 2, 'v' => 'sd2']
        ];
        $dataMock = $this->getMock('Evoke\Model\Data\FlatIface');
        $dataMock
            ->expects($this->once())
            ->method('setData')
            ->with($expected);

        $obj = new TestExtendedDecorator($dataMock);
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

        $obj = new TestExtendedDecorator($dataMock);
        $this->assertSame($expected, $obj->valid());
    }
}
// EOF
