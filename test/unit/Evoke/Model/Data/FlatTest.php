<?php
namespace Evoke_Test\Model\Data\FlatTest;

use Evoke\Model\Data\Flat,
    PHPUnit_Framework_TestCase;

/**
 * @covers Evoke\Model\Data\Flat
 */
class FlatTest extends PHPUnit_Framework_TestCase
{
    /******************/
    /* Data Providers */
    /******************/

    public function providerRecords()
    {
        return [
            'Two_Records' => [
                'Raw_Data' => ['K0' => ['One' => 1, 'Two' => 2],
                               'K1' => ['One' => 8, 'Two' => 9]]]];
    }

    /*********/
    /* Tests */
    /*********/

    /**
     * Test that we can check for a field in the data.
     *
     * @dataProvider providerRecords
     */
    public function testCheckField($rawData)
    {
        $object = new Flat;
        $object->setData($rawData);

        $this->assertTrue(isset($object['One'], $object['Two']));
        $this->assertFalse(isset($object['Non-exsistant']));
    }

    /**
     * Test that we can get each record in a set of data.
     *
     * @dataProvider providerRecords
     */
    public function testGetRecords($rawData)
    {
        $object = new Flat;
        $object->setData($rawData);
        $count = 0;

        foreach ($object as $testKey => $testData)
        {
            $key = 'K' . $count++;
            $this->assertSame($key, $testKey);
            $this->assertSame($rawData[$key], $testData->getRecord());
        }
    }

    /**
     * Test that data can be accessed by field and that we start from the first
     * record.
     *
     * @dataProvider providerRecords
     */
    public function testOffsetGet($rawData)
    {
        $object = new Flat;
        $object->setData($rawData);

        $this->assertSame(1, $object['One']);
    }

    /**
     * Test Loop over empty records.
     */
    public function testLoopEmpty()
    {
        $object = new Flat;

        foreach ($object as $data)
        {
            // Shouldn't get in here.
            throw new \Exception('Empty data shouldn\'t enter foreach.');
        }

        $this->assertTrue(true);
    }

    /**
     * Test that we can't set individual fields in the data.
     *
     * @dataProvider      providerRecords
     * @expectedException BadMethodCallException
     */
    public function testSetFails($rawData)
    {
        $object = new Flat;
        $object->setData($rawData);
        $object['One'] = 'Onety1';
    }

    /**
     * Test that we start from empty data.
     */
    public function testStartEmpty()
    {
        $object = new Flat;

        $this->assertTrue($object->isEmpty());
    }

    /**
     * Test that we can't unset individual fields in the data.
     *
     * @dataProvider      providerRecords
     * @expectedException BadMethodCallException
     */
    public function testUnsetFails($rawData)
    {
        $object = new Flat;
        $object->setData($rawData);
        unset($object['One']);
    }
}
// EOF