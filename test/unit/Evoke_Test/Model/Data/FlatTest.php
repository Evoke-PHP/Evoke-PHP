<?php
namespace Evoke_Test\Model\Data\FlatTest;

use Evoke\Model\Data\Flat;
use PHPUnit_Framework_TestCase;

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
            'two_records' => [
                'raw_data' => [
                    'k0' => ['one' => 1, 'two' => 2],
                    'k1' => ['one' => 8, 'two' => 9]
                ]
            ]
        ];
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

        $this->assertTrue(isset($object['one'], $object['two']));
        $this->assertFalse(isset($object['Non-existent']));
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

        foreach ($object as $testKey => $testData) {
            $key = 'k' . $count++;
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

        $this->assertSame(1, $object['one']);
    }

    /**
     * Test Loop over empty records.
     */
    public function testLoopEmpty()
    {
        $object = new Flat;

        foreach ($object as $data) {
            // Shouldn't get in here.
            throw new \Exception("Empty data shouldn't enter foreach.");
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
        $object['one'] = 'onety1';
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
        unset($object['one']);
    }
}
// EOF
