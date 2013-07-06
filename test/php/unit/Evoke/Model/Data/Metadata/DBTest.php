<?php
namespace Evoke_Test\Model\Data\Metadata\DBTest;

use Evoke\Model\Data\Metadata\DB,
	PHPUnit_Framework_TestCase;

class DBTest extends PHPUnit_Framework_TestCase
{ 
	/******************/
	/* Data Providers */
	/******************/

	public function providerGetJoinID()
	{
		$metadataMock = $this->getMockBuilder('Evoke\Model\Data\Metadata\DB')
			->disableOriginalConstructor()
			->getMock();
		
		$object = new DB(['ID', 'List_ID'],
		                 ['List_ID=T2.F2'              => $metadataMock,
		                  'One=Single.Word'            => $metadataMock,
		                  'Three_Word_Join=T3.F3'      => $metadataMock,
		                  'alreadyCamel=tCamel.fCamel' => $metadataMock],
		                 [],
		                 'T',
		                 'T');

		return [
			'Camel_With_Appended_ID' => [
				'Expected' => 'List_ID=T2.F2',
				'Join'     => 'listID',
				'Object'   => $object],
			'Pascal_Exact'           => [
				'Expected' => 'Three_Word_Join=T3.F3',
				'Join'     => 'Three_Word_Join=T3.F3',
				'Object'   => $object]];
	}

	public function providerGood()
	{
		return [
			'No_Joins' => [
				'Fields'       => ['ID', 'One', 'Two'],
				'Joins'        => [],
				'Primary_Keys' => ['ID'],
				'Table_Alias'  => 'Num',
				'Table_Name'   => 'Number']
			];
	}
	
	/*********/
	/* Tests */
	/*********/

	/**
	 * Test that a metadata object can be constructed.
	 *
	 * @covers       Evoke\Model\Data\Metadata\DB::__construct
	 * @dataProvider providerGood
	 */
	public function test__construct(
		$fields, $joins, $primaryKeys, $tableAlias, $tableName)
	{
		$this->assertInstanceOf(
			'Evoke\Model\Data\Metadata\DB',
			new DB($fields, $joins, $primaryKeys, $tableAlias, $tableName));
 	}

	/**
	 * Test that the metadata can arrange flat data into a hierarchical
	 * structure.
	 *
	 * @covers  Evoke\Model\Data\Metadata\DB::arrangeFlatData
	 * @covers  Evoke\Model\Data\Metadata\DB::filterFields
	 * @covers  Evoke\Model\Data\Metadata\DB::getRowID
	 * @covers  Evoke\Model\Data\Metadata\DB::isResult
	 * @depends test__construct
	 */
	public function testArrangeFlatData()
	{
		$metadataTwo = new DB(['Dos', 'List_ID', 'Two'],
		                      [],
		                      ['Dos'],
		                      'T2nd',
		                      'Second');
		
		$object = new DB(['Uno', 'One', 'List_ID'],
		                 ['List_ID=T2nd.List_ID' => $metadataTwo],
		                 ['Uno'],
		                 'T1st',
		                 'First');

		$data = [
			['T1st.Uno'     => 1,
			 'T1st.One'     => 'One 1',
			 'T1st.List_ID' => 2,
			 'T2nd.Dos'     => 1,
			 'T2nd.List_ID' => 2,
			 'T2nd.Two'     => 'Two 1'],
			['T1st.Uno'     => 1,
			 'T1st.One'     => 'One 1',
			 'T1st.List_ID' => 2,
			 'T2nd.Dos'     => 2,
			 'T2nd.List_ID' => 2,
			 'T2nd.Two'     => 'Two 2'],
			['T1st.Uno'     => 2,
			 'T1st.One'     => 'One 2',
			 'T1st.List_ID' => NULL,
			 'T2nd.Dos'     => NULL,
			 'T2nd.List_ID' => NULL,
			 'T2nd.Dos'     => NULL]];

		$expectedData = [
			1 => ['Uno'        => 1,
			      'One'        => 'One 1',
			      'List_ID'    => 2,
			      'Joint_Data' => [
				      'List_ID=T2nd.List_ID' => [
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
				      'List_ID=T2nd.List_ID' => []]]];
		
		$this->assertSame($expectedData, $object->arrangeFlatData($data));			
	}

	/**
	 * Test that we can get join ID's appropriately.
	 *
	 * @covers       Evoke\Model\Data\Metadata\DB::getJoinID
	 * @covers       Evoke\Model\Data\Metadata\DB::getJoinParentField
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
	 * @expectedException Exception
	 */
	public function testGetJoinIDAmbiguous()
	{
		$metadataMock = $this->getMock('Evoke\Model\Data\Metadata\DB');
		$object = new DB(['ID', 'List_ID'],
		                 ['List_ID=T2.F2'         => $metadataMock,
		                  'One=Single.Word'       => $metadataMock,
		                  'Three_Word_Join=T3.F3' => $metadataMock,
		                  'Three_Word_Join=T4.F4' => $metadataMock],
		                 [],
		                 'T',
		                 'T');
		$object->getJoinID('threeWordJoin');
	}

	/**
	 * Test that trying to get a join that doesn't exist throws an exception.
	 *
	 * @covers            Evoke\Model\Data\Metadata\DB::getJoinID
	 * @expectedException Exception
	 */
	public function testGetJoinIDNotFound()
	{
		$metadataMock = $this->getMock('Evoke\Model\Data\Metadata\DB');
		$object = new DB(['ID', 'List_ID'],
		                 ['List_ID=T2.F2'         => $metadataMock,
		                  'One=Single.Word'       => $metadataMock,
		                  'Three_Word_Join=T3.F3' => $metadataMock,
		                  'Three_Word_Join=T4.F4' => $metadataMock],
		                 [],
		                 'T',
		                 'T');
		$object->getJoinID('notFoundJoin');
	}	
}
// EOF