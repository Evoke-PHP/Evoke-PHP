<?php
namespace Evoke_Test\Model\Data\FlatTest;

use Evoke\Model\Data\Flat,
	PHPUnit_Framework_TestCase;

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
	 * @covers       Evoke\Model\Data\Flat::offsetExists
	 * @covers       Evoke\Model\Data\Flat::setData
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
	 * @covers       Evoke\Model\Data\Flat::current
	 * @covers 		 Evoke\Model\Data\Flat::getRecord
	 * @covers       Evoke\Model\Data\Flat::key
	 * @covers       Evoke\Model\Data\Flat::next
	 * @covers       Evoke\Model\Data\Flat::rewind
	 * @covers 		 Evoke\Model\Data\Flat::setData
	 * @covers       Evoke\Model\Data\Flat::setRecord
	 * @covers       Evoke\Model\Data\Flat::valid
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
			$this->assertEquals($key, $testKey);
			$this->assertEquals($rawData[$key], $testData->getRecord());
		}
	}
	
	/**
	 * Test that data can be accessed by field and that we start from the first
	 * record.
	 *
	 * @covers 		 Evoke\Model\Data\Flat::offsetGet
	 * @covers 		 Evoke\Model\Data\Flat::setData
	 * @covers       Evoke\Model\Data\Flat::setRecord
	 * @dataProvider providerRecords
	 */
	public function testOffsetGet($rawData)
	{
		$object = new Flat;
		$object->setData($rawData);
		
		$this->assertEquals(1, $object['One']);
	}

	/**
	 * Test Loop over empty records.
	 *
	 * @covers Evoke\Model\Data\Flat::rewind
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
	 * @covers            Evoke\Model\Data\Flat::offsetSet
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
	 *
	 * @covers Evoke\Model\Data\Flat::isEmpty
	 */
	public function testStartEmpty()
	{
		$object = new Flat;

		$this->assertTrue($object->isEmpty());
	}

	/**
	 * Test that we can't unset individual fields in the data.
	 *
	 * @covers            Evoke\Model\Data\Flat::offsetUnset
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