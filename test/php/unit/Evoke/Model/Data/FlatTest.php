<?php
namespace Evoke_Test\Model\Data\FlatTest;

use Evoke\Model\Data\Flat,
	PHPUnit_Framework_TestCase;

class FlatTest extends PHPUnit_Framework_TestCase
{ 
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

	public function providerArrangeFlatDataSingleLevel()
	{
		return [
			['Flat_Results'  => [['T1.F1' => 1, 'T1.F2' => 2, 'T2.F1' => 3],
			                     ['T1.F1' => 4, 'T1.F2' => 5, 'T2.F1' => 6]],
			 'Expected_Data' => [['T1.F1' => 1, 'T1.F2' => 2],
			                     ['T1.F1' => 4, 'T1.F2' => 5]]]];
	}

	/*********/
	/* Tests */
	/*********/

	/**
	 * Test the construction of a good object.
	 *
	 * @covers       Evoke\Model\Data\Flat::__construct
	 * @dataProvider provider__constructGood
	 */
	public function test__constructGood($data      = NULL,
	                                    $dataJoins = NULL,
	                                    $jointKey  = NULL)
	{
		if (!isset($data))
		{
			$object = new Flat();
		}
		elseif (!isset($dataJoins))
		{
			$object = new Flat($data);
		}
		elseif (!isset($jointKey))
		{
			$object = new Flat($data, $dataJoins);
		}
		else
		{
			$object = new Flat($data, $dataJoins, $jointKey);
		}
		
		$this->assertInstanceOf('Evoke\Model\Data\Flat', $object);
	}

	/**
	 * Test the construction with Invalid Arguments.
	 *
	 * @covers            Evoke\Model\Data\Flat::__construct
	 * @dataProvider      provider__constructInvalidArguements
	 * @expectedException InvalidArgumentException
	 */
	public function test__constructInvalidArguments($data      = NULL,
	                                                $dataJoins = NULL,
	                                                $jointKey  = NULL)
	{
		if (!isset($data))
		{
			$object = new Flat();
		}
		elseif (!isset($dataJoins))
		{
			$object = new Flat($data);
		}
		elseif (!isset($jointKey))
		{
			$object = new Flat($data, $dataJoins);
		}
		else
		{
			$object = new Flat($data, $dataJoins, $jointKey);
		}
	}

	/**
	 * Test the BAD retrieval joint data.
	 *
	 * @covers            Evoke\Model\Data\Flat::__get
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
	 * @covers       Evoke\Model\Data\Flat::__get
	 * @covers       Evoke\Model\Data\Flat::getJoinName
	 * @covers       Evoke\Model\Data\Flat::setRecord
	 * @depends      test__constructGood
	 * @dataProvider provider__getGood
	 */
	public function test__getGood($object, $parentField, $expected)
	{
		$jointData = $object->$parentField;
		$this->assertSame($expected, $jointData);
	}

	/**
	 * Ensure that flat result data is filtered to provide only the appropriate
	 * fields for the single level data structure.
	 *
	 * @covers       Evoke\Model\Data\Flat::arrangeFlatData
	 * @dataProvider providerArrangeFlatDataSingleLevel
	 */
	public function testArrangeFlatDataSingleLevel(
		Array $flatResults, $expectedData)
	{
		$object = new Flat([], ['Table_Name'      => 'T1',
		                        'Table_Separator' => '.']);

		$this->assertEquals($expectedData,
		                    $object->arrangeFlatData($flatResults));
	}
	
	/**
	 * Test that multiple records can be iterated.  The children for each
	 * single record should contain only the joint data that is appropriate.
	 *
	 *
	 * @covers       Evoke\Model\Data\Data::__get
	 * @covers       Evoke\Model\Data\Data::getJoinName
	 * @covers       Evoke\Model\Data\Data::setRecord
	 * @depends      test__constructGood
	 */
	public function testNextChildren()
	{
		$data = [
			1 => ['ID' => 1,
			      'Name' => 'admin',
			      'Password' => '$2y$aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa',
			      'Joint_Data' => 
			      ['ID' =>
			       ['' => ['User_ID' => 1,
			               'Role_ID' => 1,
			               'Joint_Data' => 
			               ['Role_ID' => 
			                [1 => ['ID' => 1,
			                       'Name' => 'Admin',
			                       'Joint_Data' =>
			                       ['ID' =>
			                        ['' => ['Role_ID' => 1,
			                                'Permission_ID' => 1,
			                                'Joint_Data' => 
			                                ['Permission_ID' => 
			                                 [1 => ['ID' => 1,
			                                        'Name' => 'Create']]]]]]]]]
					       ]]]],
				                            
			2 => ['ID' => 2,
			      'Name' => 'temp',
			      'Password' => '$2y$bbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbb',
			      'Joint_Data' => 
			      ['ID' => []]]];


		$object = new Data(
			$data,
			['ID' => new Data(
					[],
					['Role_ID' => new Data(
							[],
							['ID' => new Data(
									[],
									['Permission_ID' => new Data()])
								])
						])
				]);
		$object->setData($data);

		$this->assertSame('Create',
		                  $object->ID->Role_ID->ID->Permission_ID['Name']);

		$object->next();
		$this->assertSame('temp', $object['Name']);


		$this->assertTrue($object->ID->Role_ID->ID->Permission_ID->isEmpty());
		$this->assertNull($object->ID->Role_ID->ID->Permission_ID['Name'],
		                  'Next child should have a NULL result.');
	}
}
// EOF
