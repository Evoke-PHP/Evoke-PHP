<?php
namespace Evoke_Test\Model\Data\Metadata\DBTest;

use Evoke\Model\Data\Metadata\DB,
	PHPUnit_Framework_TestCase;

class DBTest extends PHPUnit_Framework_TestCase
{ 
	/******************/
	/* Data Providers */
	/******************/

	public function providerCreateGood()
	{
		return [
			'No_Joins' => [
				'Fields'       => ['ID', 'One', 'Two'],
				'Joins'        => [],
				'Primary_Keys' => ['ID'],
				'Table_Name'   => 'Number',
				'Table_Alias'  => 'Num']
			];
	}
	
	public function providerGetJoinID()
	{
		$metadataMock = $this->getMockBuilder('Evoke\Model\Data\Metadata\DB')
			->disableOriginalConstructor()
			->getMock();
		
		$object = new DB(['ID', 'List_ID'],
		                 ['List_ID=List_T_ID'       => $metadataMock,
		                  'Two_Pascal=Two_T_Pascal' => $metadataMock,
		                  'Three_Word_Join=T3_T_3'  => $metadataMock,
		                  'Simple_Join_No_Details'  => $metadataMock],
		                 ['ID'],
		                 'T');

		return [
			'Lower_Camel'            => [
				'Expected' => 'List_ID=List_T_ID',
				'Join'     => 'listID',
				'Object'   => $object],
			'Pascal'                 => [
				'Expected' => 'Two_Pascal=Two_T_Pascal',
				'Join'     => 'Two_Pascal',
				'Object'   => $object],
			'Pascal_Exact'           => [
				'Expected' => 'Three_Word_Join=T3_T_3',
				'Join'     => 'Three_Word_Join=T3_T_3',
				'Object'   => $object],
			'Simple_Join_No_Details' => [
				'Expected' => 'Simple_Join_No_Details',
				'Join'     => 'simpleJoinNoDetails',
				'Object'   => $object]];
	}

	public function providerPrimaryKeyBadData()
	{
		return [
			'Joint'  => [
				'Object'       => new DB(['One'],
				                         ['Two_List=Two.List_ID' => new DB(
						                         ['Two'],
						                         [],
						                         ['ID_One', 'ID_Two'],
						                         'Two')],
				                         ['ID'],
				                         'One'),
				'Flat_Results' => [
					['One_T_One'    => 'Good',
					 'Two_T_Two'    => 'Good',
					 'Two_T_ID_One' => 'Good',
					 'Two_T_ID_Two' => 'Good'],
					['One_T_One'    => 'Good',
					 'Two_T_Two'    => 'Good',
					 'Two_T_ID_Two' => 'Bad Missing Two_T_ID_One']]],					
			'Simple' => [
				'Object'       => new DB(['One'],
				                         [],
				                         ['ID'],
				                         'T'),
				'Flat_Results' => [
					['T_T_One' => 'Good',
					 'T_T_ID'  => 1],
					['T_T_One' => 'Bad']]]
			];
	}
	
	/*********/
	/* Tests */
	/*********/

	/**
	 * Test that the metadata can arrange flat data into a hierarchical
	 * structure.
	 *
	 * @covers  Evoke\Model\Data\Metadata\DB::arrangeFlatData
	 * @covers  Evoke\Model\Data\Metadata\DB::arrangeSplitResults
	 * @covers  Evoke\Model\Data\Metadata\DB::getRowID
	 * @covers  Evoke\Model\Data\Metadata\DB::isResult
	 * @covers  Evoke\Model\Data\Metadata\DB::splitResultByTables
	 */
	public function testArrangeFlatData()
	{
		$metadataTwo = new DB(['Dos', 'List_ID', 'Two'],
		                      [],
		                      ['Dos'],
		                      'T2nd');
		
		$object = new DB(['Uno', 'One', 'List_ID'],
		                 ['List_ID' => $metadataTwo],
		                 ['Uno'],
		                 'T1st');

		$data = [
			['T1st_T_Uno'     => 1,
			 'T1st_T_One'     => 'One 1',
			 'T1st_T_List_ID' => 2,
			 'T2nd_T_Dos'     => 1,
			 'T2nd_T_List_ID' => 2,
			 'T2nd_T_Two'     => 'Two 1'],
			['T1st_T_Uno'     => 1,
			 'T1st_T_One'     => 'One 1',
			 'T1st_T_List_ID' => 2,
			 'T2nd_T_Dos'     => 2,
			 'T2nd_T_List_ID' => 2,
			 'T2nd_T_Two'     => 'Two 2'],
			['T1st_T_Uno'     => 2,
			 'T1st_T_One'     => 'One 2',
			 'T1st_T_List_ID' => NULL,
			 'T2nd_T_Dos'     => NULL,
			 'T2nd_T_List_ID' => NULL,
			 'T2nd_T_Dos'     => NULL]];

		$expectedData = [
			1 => ['Uno'        => 1,
			      'One'        => 'One 1',
			      'List_ID'    => 2,
			      'Joint_Data' => [
				      'List_ID' => [
					      1 => ['Dos'     => 1,
					            'List_ID' => 2,
					            'Two'     => 'Two 1'],
					      2 => ['Dos'     => 2,
					            'List_ID' => 2,
					            'Two'     => 'Two 2']]]],
			2 => ['Uno'     => 2,
			      'One'     => 'One 2',
			      'List_ID' => NULL,
			      'Joint_Data' => [
				      'List_ID' => []]]];
		
		$this->assertSame($expectedData, $object->arrangeFlatData($data));			
	}

	/**
	 * Test that a metadata object can be constructed.
	 *
	 * @covers       Evoke\Model\Data\Metadata\DB::__construct
	 * @dataProvider providerCreateGood
	 */
	public function testCreateGood(
		$fields, $joins, $primaryKeys, $tableName, $tableAlias)
	{
		$this->assertInstanceOf(
			'Evoke\Model\Data\Metadata\DB',
			new DB($fields, $joins, $primaryKeys, $tableName, $tableAlias));
 	}

	/**
	 * @covers                   Evoke\Model\Data\Metadata\DB::__construct
	 * @expectedException        InvalidArgumentException
	 * @expectedExceptionMessage Primary Keys cannot be empty.
	 */
	public function testCreatePrimaryKeyMissing()
	{
		$object = new DB(['ID', 'One', 'Two'],
		                 [],
		                 [], // Missing Primary Key
		                 'Table_One');
	}

	/**
	 * Test that we can get join ID's appropriately.
	 *
	 * @covers       Evoke\Model\Data\Metadata\DB::getJoinID
	 * @dataProvider providerGetJoinID
	 */
	public function testGetJoinID($expected, $join, $object)
	{
		$this->assertSame($expected, $object->getJoinID($join));
	}

	/**
	 * Test that trying to get a join that is ambiguous throws an exception.
	 *
	 * @covers            Evoke\Model\Data\Metadata\DB::getJoinID
	 * @expectedException DomainException
	 */
	public function testGetJoinIDAmbiguous()
	{
		$metadataMock = $this->getMockBuilder('Evoke\Model\Data\Metadata\DB')
			->disableOriginalConstructor()
			->getMock();
		
		$object = new DB(['List_ID'],
		                 ['List_ID=T2.F2'         => $metadataMock,
		                  'One=Single.Word'       => $metadataMock,
		                  'Three_Word_Join=T3.F3' => $metadataMock,
		                  'Three_Word_Join=T4.F4' => $metadataMock],
		                 ['ID'],
		                 'T',
		                 'T');
		$object->getJoinID('threeWordJoin');
	}
	
	/**
	 * Test that trying to get a join that doesn't exist throws an exception.
	 *
	 * @covers                   Evoke\Model\Data\Metadata\DB::getJoinID
	 * @expectedException        DomainException
	 * @expectedExceptionMessage Join not found
	 */
	public function testGetJoinIDNotFound()
	{
		$metadataMock = $this->getMockBuilder('Evoke\Model\Data\Metadata\DB')
			->disableOriginalConstructor()
			->getMock();
		$object = new DB(['ID', 'List_ID'],
		                 ['List_ID'         => $metadataMock,
		                  'One'             => $metadataMock,
		                  'Three_Word_Join' => $metadataMock],
		                 ['ID'],
		                 'T');
		$object->getJoinID('notFoundJoin');
	}

	/**
	 * @covers                   Evoke\Model\Data\Metadata\DB::arrangeFlatData
	 * @covers                   Evoke\Model\Data\Metadata\DB::getRowID
	 * @expectedException        DomainException
	 * @expectedExceptionMessage Missing Primary Key
	 */
	public function testGetRowIDMissingPrimaryKey()
	{
		$object = new DB(['Any'],
		                 [],
		                 ['ID'],
		                 'One');
		$object->arrangeFlatData([['One_T_Any' => 'Good',
		                           'One_T_ID'  => NULL]]);
	}
	
	/**
	 * @covers       Evoke\Model\Data\Metadata\DB::arrangeFlatData
	 * @covers       Evoke\Model\Data\Metadata\DB::splitResultByTables
	 * @dataProvider providerPrimaryKeyBadData
	 * @expectedException        DomainException
	 * @expectedExceptionMessage Missing Primary Key
	 */
	public function testPrimaryKeyBadData(DB $object, Array $flatResults)
	{
		$object->arrangeFlatData($flatResults);
	}
	
	/**
	 * @covers            Evoke\Model\Data\Metadata\DB::splitResultByTables
	 * @expectedException DomainException
	 */
	public function testSplitResultBadData()
	{
		$object = new DB(['One'],
		                 [],
		                 ['ID'],
		                 ['T']);
		$object->arrangeFlatData(
			[['T_T_One' => 'Good',
			  'Bad'     => 'Key should have format: <Table><Separator><Field>']]);
	}
}
// EOF