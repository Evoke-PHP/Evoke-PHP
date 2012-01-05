<?php
class DataTest extends PHPUnit_Framework_TestCase
{ 
   /** @covers Data::__construct
    */
   public function test__construct()
   {
      $recordMock = $this->getMockBuilder('Record')
	 ->disableOriginalConstructor()
	 ->getMock();
      
      $obj = new Data(array('Record' => $recordMock));
      $this->assertInstanceOf('Data', $obj);

      // Show that the record object must be supplied.
      try
      {
	 $obj = new Data(array('No_REC' => 'Supplied'));
	 $this->fail('Record object must be supplied.');
      }
      catch (Exception $e)
      {
	 $this->assertTrue(true, 'Exception was raised.');
      }
   }

   /** @covers Data::setData
    */
   public function testSetData()
   {
      $testData = array(array('ID' => 1,
			      'Ch' => 'A'),
			array('ID' => 2,
			      'Ch' => 'B'));
			
      
      $recordMock = $this->getMockBuilder('Record')
	 ->disableOriginalConstructor()
	 ->getMock();

      $recordMock->expects($this->at(0))
	 ->method('setRecord')
	 ->with($testData[0]);
	 
      $obj = new Data(array('Record' => $recordMock));
      $obj->setData($testData);

      
      // Ensure that data must be an array of arrays (an array of records).
      try
      {
	 $failData = array(1, 2, 3);
	 $obj->setData($failData);
	 $this->fail('Data must be an array of arrays');
      }
      catch (Exception $e)
      {
	 $this->assertTrue(true, 'Exception was raised.');
      }
   }

   /**  @covers Data::current
    *   @covers Data::key
    *   @covers Data::next
    *   @covers Data::rewind
    *   @covers Data::valid
    */
   public function testIterator()
   {
      $testData = array(
	 'First'  => array('RA'         => 'A',
			   'RB'         => 'B',
			   'One_ID'     => 1,
			   'Two_ID'     => 2,
			   'Joint_Data' => array(
			      'One_ID' => array(
				 array('ID'     => 1,
				       'One_ID' => 1),
				 array('ID'     => 3,
				       'One_ID' => 1)),
			      'Two_ID' => array(
				 array('ID'     => 2,
				       'Two_ID' => 2),
				 array('ID'     => 4,
				       'Two_ID' => 2)))),
	 'Second' => array('RA'         => 'A',
			   'RB'         => 'B',
			   'One_ID'     => 1,
			   'Two_ID'     => 2,
			   'Joint_Data' => array(
			      'One_ID' => array(
				 array('ID'     => 1,
				       'One_ID' => 1),
				 array('ID'     => 3,
				       'One_ID' => 1)),
			      'Two_ID' => array(
				 array('ID'     => 2,
				       'Two_ID' => 2),
				 array('ID'     => 4,
				       'Two_ID' => 2)))));

      $recordMock = $this->getMockBuilder('Record')
	 ->disableOriginalConstructor()
	 ->getMock();

      $recordIndex = 0;
      
      // Once for the set data.
      $recordMock->expects($this->at($recordIndex++))
	 ->method('setRecord')
	 ->with($testData['First']);

      // Again for the rewind at the start of the foreach.
      $recordMock->expects($this->at($recordIndex++))
	 ->method('setRecord')
	 ->with($testData['First']);
      
      // Next item.
      $recordMock->expects($this->at($recordIndex++))
	 ->method('setRecord')
	 ->with($testData['Second']);
      
      $obj = new Data(array('Record' => $recordMock));
      $obj->setData($testData);

      $expected = array(array('Key'   => 'First',
			      'Value' => $recordMock),
			array('Key'   => 'Second',
			      'Value' => $recordMock));
      $i = 0;
			      
      foreach ($obj as $dataKey => $dataValue)
      {
	 $this->assertEquals($expected[$i]['Key'],
			     $dataKey,
			     'Unexpected key while iterating'); 
	 $this->assertEquals($expected[$i++]['Value'],
			     $dataValue,
			     'Unexpected value while iterating');
      }
   }


   /**  @covers Data::current
    *   @covers Data::key
    *   @covers Data::next
    *   @covers Data::rewind
    *   @covers Data::valid
    */
   public function testManualIteration()
   {
      $testData = array(
	 'First'  => array('RA'         => 'A',
			   'RB'         => 'B',
			   'One_ID'     => 1,
			   'Two_ID'     => 2,
			   'Joint_Data' => array(
			      'One_ID' => array(
				 array('ID'     => 1,
				       'One_ID' => 1),
				 array('ID'     => 3,
				       'One_ID' => 1)),
			      'Two_ID' => array(
				 array('ID'     => 2,
				       'Two_ID' => 2),
				 array('ID'     => 4,
				       'Two_ID' => 2)))),
	 'Second' => array('RA'         => 'A',
			   'RB'         => 'B',
			   'One_ID'     => 1,
			   'Two_ID'     => 2,
			   'Joint_Data' => array(
			      'One_ID' => array(
				 array('ID'     => 1,
				       'One_ID' => 1),
				 array('ID'     => 3,
				       'One_ID' => 1)),
			      'Two_ID' => array(
				 array('ID'     => 2,
				       'Two_ID' => 2),
				 array('ID'     => 4,
				       'Two_ID' => 2)))));

      $recordIndex = 0;
      $recordMock = $this->getMockBuilder('Record')
	 ->disableOriginalConstructor()
	 ->getMock();

      // Once for initial setData.
      $recordMock->expects($this->at($recordIndex++))
	 ->method('setRecord')
	 ->with($testData['First']);
      $recordMock->expects($this->at($recordIndex++))
	 ->method('setRecord')
	 ->with($testData['Second']);

      // Rewind.
      $recordMock->expects($this->at($recordIndex++))
	 ->method('setRecord')
	 ->with($testData['First']);
      $recordMock->expects($this->at($recordIndex++))
	 ->method('setRecord')
	 ->with($testData['Second']);
      
      
      $obj = new Data(array('Record' => $recordMock));
      $obj->setData($testData);
      
      $this->assertEquals('First', $obj->key(), 'Starts at first item key.');
      $this->assertEquals(
	 $recordMock, $obj->current(), 'Reset to first value.');
      $this->assertEquals($recordMock, $obj->next(), 'Next value.');

      $obj->rewind();
      $obj->next();
      $this->assertEquals(false, $obj->next(), 'Next past last is false');
      $this->assertEquals(false, $obj->valid(), 'Valid past last is false');
   }
}
// EOF