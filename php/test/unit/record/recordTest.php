<?php
class RecordTest extends PHPUnit_Framework_TestCase
{ 
   /** @covers Record::__construct
    */
   public function test__construct()
   {
      $recordMock = $this->getMockBuilder('Record')
	 ->disableOriginalConstructor()
	 ->getMock();
      
      $obj = new Record(
	 array('References' => array(
		  'Parent_Field' => new Data(array('Record' => $recordMock)))));
      $this->assertInstanceOf('Record', $obj);

      // Show that the references must be supplied.
      try
      {
	 $obj = new Record(array('NO_REFS' => 'SUPPLIED'));
	 $this->fail('References must be supplied when constructing.');
      }
      catch (Exception $e)
      {
	 $this->assertTrue(true, 'Exception was raised.');
      }
      
      // Show that references must be to data container objects.
      try
      {
	 $obj = new Record(array('References' => array(
				    'Parent_Field' => $this->getMock('BAD'))));
	 $this->fail('Exception should be raised for references that do not ' .
		     'have a data container.');
      }
      catch (Exception $e)
      {
	 $this->assertTrue(true, 'Exception was raised.');
      }
   }

   /** @covers Record::__get
    *  @covers Record::getReferenceName
    */
   public function test__get()
   {
      // Test that an exception is raised when we try to get something that is
      // not a reference.
      $obj = new Record(array('References' => array()));

      try
      {
	 $x = $obj->parentField;
	 $this->fail('Should not be able to get a non-reference.');
      }
      catch (Exception $e)
      {
	 $this->assertTrue(true, 'Exception was raised.');
      }

      $dataMock = $this->getMockBuilder('Data')
	 ->disableOriginalConstructor()
	 ->getMock();
      
      // Test that references are returned using both the original parent field
      // and the renamed parent field.
      $obj = new Record(array('References' => array(
				 'Parent_Field'    => $dataMock,
				 'Field_End_In_ID' => $dataMock)));
      $this->assertEquals($dataMock,
			  $obj->Parent_Field,
			  'Reference by Original Parent Field.');
      $this->assertEquals($dataMock,
			  $obj->parentField,
			  'Reference by renamed Parent Field.');
      $this->assertEquals($dataMock,
			  $obj->Field_End_In_ID,
			  'Reference by Original Field End In ID.');
      $this->assertEquals($dataMock,
			  $obj->fieldEndIn, // The last ID is removed by rename.
			  'Reference by renamed Field End In ID.');
   }

   /** @covers Record::setRecord
    */
   public function testSetRecord()
   {
      $recordData = array(
	 'RA'         => 'A',
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
		     'Two_ID' => 2))));	       

      $oneData = $this->getMockBuilder('Data')
	 ->disableOriginalConstructor()
	 ->getMock();
      $twoData = $this->getMockBuilder('Data')
	 ->disableOriginalConstructor()
	 ->getMock();
      
      $oneData
	 ->expects($this->once())
	 ->method('setData')
	 ->with($recordData['Joint_Data']['One_ID']);

      $twoData
	 ->expects($this->once())
	 ->method('setData')
	 ->with($recordData['Joint_Data']['Two_ID']);
      
      $obj = new Record(array('References' => array('One_ID' => $oneData,
						    'Two_ID' => $twoData)));
      $obj->setRecord($recordData);      
   }

   /**  @covers Record::current
    *   @covers Record::key
    *   @covers Record::next
    *   @covers Record::rewind
    *   @covers Record::valid
    */
   public function testIterator()
   {
      $recordData = array(
	 'RA'         => 'A',
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
		     'Two_ID' => 2))));	       

      $oneData = $this->getMockBuilder('Data')
	 ->disableOriginalConstructor()
	 ->getMock();
      $twoData = $this->getMockBuilder('Data')
	 ->disableOriginalConstructor()
	 ->getMock();

      $obj = new Record(array('References' => array('One_ID' => $oneData,
						    'Two_ID' => $twoData)));
      $obj->setRecord($recordData);

      $expected = array(array('Key'   => 'RA',
			      'Value' => 'A'),
			array('Key'   => 'RB',
			      'Value' => 'B'),
			array('Key'   => 'One_ID',
			      'Value' => '1'),
			array('Key'   => 'Two_ID',
			      'Value' => '2'));
      $i = 0;
      
      foreach ($obj as $recordKey => $recordItem)
      {
	 $this->assertEquals($expected[$i]['Key'],
			     $recordKey,
			     'Unexpected key while iterating.');
	 $this->assertEquals($expected[$i++]['Value'],
			     $recordItem,
			     'Unexpected value while iterating');			    
      }

      // Manual iteration.
      $obj->rewind();
      $this->assertEquals('RA', $obj->key(), 'Reset to first item key.');
      $this->assertEquals('A', $obj->current(), 'Reset to first value.');
      $this->assertEquals('B', $obj->next(), 'Next value.');
   }
}
// EOF