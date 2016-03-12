<?php
namespace Evoke_Test\Model\Data;

use Evoke\Model\Data\RecordList;
use PHPUnit_Framework_TestCase;

/**
 * @covers Evoke\Model\Data\RecordList
 * @uses   Evoke\Model\Data\Decorator
 */
class RecordListTest extends PHPUnit_Framework_TestCase
{
    /*********/
    /* Tests */
    /*********/

    public function testFirstRecordSelected()
    {
        $rawData  = [
            ['id' => 1, 'text' => 'first'],
            ['id' => 2, 'text' => 'second']
        ];
        $dIndex   = 0;
        $dataMock = $this->getMock('Evoke\Model\Data\FlatIface');

        $dataMock
            ->expects($this->at($dIndex++))
            ->method('setData')
            ->with($rawData);
        $dataMock
            ->expects($this->at($dIndex++))
            ->method('getRecord')
            ->with()
            ->will($this->returnValue($rawData[0]));

        $obj = new RecordList($dataMock);
        $obj->setData($rawData);
        $obj->selectRecord($rawData[0]);

        $this->assertTrue($obj->hasSelectedRecord(), 'Should have selected record.');
        $this->assertTrue($obj->isSelectedRecord(), 'Should be the selected record.');


    }

    public function testClearSelectedRecords()
    {
        $dataMock = $this->getMock('Evoke\Model\Data\FlatIface');
        $obj      = new RecordList($dataMock);

        $obj->selectRecord(['id' => 1]);
        $obj->selectRecord(['id' => 2]);
        $obj->clearSelectedRecords();

        $this->assertFalse($obj->hasSelectedRecord());
    }

    public function testClearSelectedRecord()
    {
        $dataMock = $this->getMock('Evoke\Model\Data\FlatIface');
        $obj      = new RecordList($dataMock);

        $obj->selectRecord(['id' => 1]);
        $obj->selectRecord(['id' => 2]);
        $obj->clearSelectedRecord(['id' => 2]);

        $this->assertTrue($obj->hasSelectedRecord());
    }

    public function testClearSelectedRecordNotPreviouslyAddedIsOK()
    {
        $dataMock = $this->getMock('Evoke\Model\Data\FlatIface');
        $obj      = new RecordList($dataMock);

        $obj->selectRecord(['id' => 1]);
        $obj->clearSelectedRecord(['id' => 3]);

        $this->assertTrue($obj->hasSelectedRecord());
    }
}
// EOF
