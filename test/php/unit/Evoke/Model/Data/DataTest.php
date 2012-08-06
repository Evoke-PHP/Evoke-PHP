<?php
namespace Evoke_Test\Model\Data\DataTest;

use Evoke\Model\Data\Data,
	PHPUnit_Framework_TestCase;

class DataTest extends PHPUnit_Framework_TestCase
{ 
	/**
	 * Test the construction of a good object.
	 *
	 * @covers       Evoke\Model\Data\Data::__construct
	 * @covers       Evoke\Model\Data\DataAbstract::__construct
	 * @dataProvider provider__constructGood
	 */
	public function test__constructGood($data      = NULL,
	                                    $dataJoins = NULL,
	                                    $jointKey  = NULL)
	{
		if (!isset($data))
		{
			$obj = new Data();
		}
		elseif (!isset($dataJoins))
		{
			$obj = new Data($data);
		}
		elseif (!isset($jointKey))
		{
			$obj = new Data($data, $dataJoins);
		}
		else
		{
			$obj = new Data($data, $dataJoins, $jointKey);
		}
		
		$this->assertInstanceOf('Evoke\Model\Data\Data', $obj);
	}

	/**
	 * Test the construction with Invalid Arguments.
	 *
	 * @covers            Evoke\Model\Data\Data::__construct
	 * @dataProvider      provider__constructInvalidArguements
	 * @expectedException InvalidArgumentException
	 */
	public function test__constructInvalidArguments($data      = NULL,
	                                                $dataJoins = NULL,
	                                                $jointKey  = NULL)
	{
		if (!isset($data))
		{
			$obj = new Data();
		}
		elseif (!isset($dataJoins))
		{
			$obj = new Data($data);
		}
		elseif (!isset($jointKey))
		{
			$obj = new Data($data, $dataJoins);
		}
		else
		{
			$obj = new Data($data, $dataJoins, $jointKey);
		}
	}

	/**
	 * Test the BAD retrieval joint data.
	 *
	 * @covers            Evoke\Model\Data\Data::__get
	 * @depends           test__constructGood
	 * @dataProvider      provider__getBad
	 * @expectedException OutOfBoundsException
	 */
	public function test__getBad($object, $parentField)
	{
		$jointData = $object->$parentField;
	}

	/**
	 * Test the GOOD retrieval of joint data.
	 *
	 * @covers       Evoke\Model\Data\Data::__get
	 * @covers       Evoke\Model\Data\Data::getJoinName
	 * @covers       Evoke\Model\Data\Data::setRecord
	 * @depends      test__constructGood
	 * @dataProvider provider__getGood
	 */
	public function test__getGood($object, $parentField, $expected)
	{
		$jointData = $object->$parentField;
		$this->assertSame($expected, $jointData);
	}
	
	/******************/
	/* Data Providers */
	/******************/

	public function provider__constructGood()
	{
		$dataContainer = $this->getMock('Evoke\Model\Data\Data');
		$dataContainer->expects($this->any())->method('setData');
		
		return array(
			'All_Defaults' =>
			array(),
			'Plain_Data' =>
			array('Data' => array(array('Record_Field' => 'Value_One'),
			                      array('Record_Field' => 'Value_Two'))),
			'Joint_Data_Default_Joint_Key' =>
			array('Data'       => array(
				      array('Rec_Field'  => 'Val',
				            'List_ID'    => 4,
				            'Joint_Data' => array(
					            'List_ID' => array(
						            array('ID'         => 4,
						                  'JRec_Field' => 'JValue'))))),
			      'Data_Joins' => array('List_ID' => $dataContainer)),
			'Joint_Data_Special_Joint_Key' =>
			array('Data'       => array(
				      array('R_Field' => 'V',
				            'Join_Me' => '3',
				            'J'       => array(
					            'Join_Me' => array(
						            array('List_ID' => 3,
						                  'JF'      => 'JV1'),
						            array('List_ID' => 3,
						                  'JF'      => 'JV2'))))),
			      'Data_Joins' => array('Join_Me' => $dataContainer),
			      'Joint_Key'  => 'J'));
	}

	public function provider__constructInvalidArguements()
	{
		return array(
			'jointKey not string' =>
			array('Data'       => array(),
			      'Data_Joins' => array(),
			      'Joint_Key'  => array('Not a String')),
			'Data_Container_Not_DataIface' =>
			array('Data' => array(),
			      'Data_Joins' => array(
				      'Parent_Field' => 'This_Should_Be_DataIface')));
	}

	
	public function provider__getBad()
	{
		$dataContainer = $this->getMock('Evoke\Model\Data\Data');
		$dataContainer->expects($this->any())->method('setData');		

		$tests = array();

		$tests['No_Join_For_Requested_Parent_Field'] =
			array('Object'       => new Data(array(),
			                                 array(
				                                 'Join_ID' => $dataContainer)),
			      'Parent_Field' => 'NOT_CORRECT');

		return $tests;
	}
	
	public function provider__getGood()
	{
		$dataContainer = $this->getMock('Evoke\Model\Data\Data');
		$dataContainer->expects($this->any())->method('setData');		
		$expected = array(array('Value'   => 'Val_One',
		                        'List_ID' => 9),
		                  array('Value'   => 'Val_Two',
		                        'List_ID' => 9));		
		$tests = array();

		// Test $object->list;
		$tests['Good_Join'] =
			array('Object'       => new Data(
				      array(array('Field'      => 'Value',
				                  'Joint_Data' => array(
					                  array('Fun'     => array(
						                        array('a' => 'b')),
					                        'List_ID' => array(
						                        array('Value'   => 'Val_One',
						                              'List_ID' => 9),
						                        array('Value'   => 'Val_Two',
						                              'List_ID' => 9)))))),
				      array('List_ID' => $dataContainer)),
			      'Parent_Field' => 'list',
			      'Expected'     => $dataContainer);

		// Test $object->List_ID;
		$tests['Good_Join_Refered_To_Differently'] = $tests['Good_Join'];
		$tests['Good_Join_Refered_To_Differently']['Parent_Field'] = 'List_ID';

		return $tests;
	}
}

// EOF