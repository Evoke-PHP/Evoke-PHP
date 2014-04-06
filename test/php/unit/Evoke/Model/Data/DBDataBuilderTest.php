<?php
namespace Evoke_Test\Model\Data\Metadata;

use Evoke\Model\Data\Data,
	Evoke\Model\Data\Metadata\DB,
	Evoke\Model\Data\DBDataBuilder,
	PHPUnit_Framework_TestCase;

class DBDataBuilderTest extends PHPUnit_Framework_TestCase
{
	/******************/
	/* Data Providers */
	/******************/

	public function providerBuild()
	{
		$t2Metadata = new DB(['ID', 'N2'],
		                     [],
		                     ['ID'],
		                     'T2',
		                     'T2');

		$mlaMetadata3 = new DB(['ID', 'N3'],
		                       [],
		                       ['ID'],
		                       'T3Aliased',
		                       'T3');
		$mlaMetadata2 = new DB(['ID', 'N2'],
		                       ['N3' => $mlaMetadata3],
		                       ['ID'],
		                       'T2',
		                       'T2');

		return [
			'Single_Table'        => [
				'Expected'             => new Data(
					new DB(['ID', 'Name'],
					       [],
					       ['ID'],
					       'Single',
					       'Single'),
					[]),
				'Fields'               => ['Single' => ['ID', 'Name']],
				'Joins'                => [],
				'Primary_Keys'         => ['Single' => ['ID']],
				'Table_Name'           => 'Single'],
			'Multiple_Table'      => [
				'Expected'             => new Data(
					new DB(['ID', 'N1'],
					       ['N2' => $t2Metadata],
					       ['ID'],
					       'T1',
					       'T1'),
					['N2' => new Data($t2Metadata, [])]),
				'Fields'               => ['T1' => ['ID', 'N1'],
				                           'T2' => ['ID', 'N2']],
				'Joins'                => ['T1' => [['Parent' => 'N2',
				                                     'Table'  => 'T2',
				                                     'Alias'  => 'T2',
				                                     'Child'  => 'ID']]],
				'Primary_Keys'         => ['T1' => ['ID'],
				                           'T2' => ['ID']],
				'Table_Name'           => 'T1'],
			'Multi_Level_Aliased' => [
				'Expected'             => new Data(
					new DB(['ID', 'N1'],
					       ['N2' => $mlaMetadata2],
					       ['ID'],
					       'T1',
					       'T1'),
					['N2' => new Data($mlaMetadata2,
					                  ['N3' => new Data($mlaMetadata3, [])])]),
				'Fields'               => ['T1'        => ['ID', 'N1'],
				                           'T2'        => ['ID', 'N2'],
				                           'T3Aliased' => ['ID', 'N3']],
				'Joins'                => ['T1' => [['Parent' => 'N2',
				                                     'Table'  => 'T2',
				                                     'Alias'  => 'T2',
				                                     'Child'  => 'ID']],
				                           'T2' => [['Parent' => 'N3',
				                                     'Table'  => 'T3',
				                                     'Alias'  => 'T3Aliased',
				                                     'Child'  => 'ID']]],
				'Primary_Keys'         => ['T1'        => ['ID'],
				                           'T2'        => ['ID'],
				                           'T3Aliased' => ['ID']],
				'Table_Name'           => 'T1']];
	}
	
	/*********/
	/* Tests */
	/*********/

	/**
	 * @covers       Evoke\Model\Data\DBDataBuilder::build
	 * @covers       Evoke\Model\Data\DBDataBuilder::buildData
	 * @covers       Evoke\Model\Data\DBDataBuilder::fillMetadataCache
	 * @dataProvider providerBuild
	 */
	public function testBuild(Data  $expected,
	                          Array $fields,
	                          Array $joins,
	                          Array $primaryKeys,
	                          /* String */ $tableName,
	                          /* String */ $tableAlias = NULL)
	{
		$obj = new DBDataBuilder;
		$this->assertEquals($expected,
		                    $obj->build($fields,
		                                $joins,
		                                $primaryKeys,
		                                $tableName,
		                                $tableAlias));
	}

	/**
	 * @covers Evoke\Model\Data\DBDataBuilder::build
	 * @covers Evoke\Model\Data\DBDataBuilder::buildData
	 * @covers Evoke\Model\Data\DBDataBuilder::fillMetadataCache	 
	 * @expectedException        InvalidArgumentException
	 * @expectedExceptionMessage Joins must have Parent and Table.
	 */
	public function testMissingJoinParent()
	{
		$obj = new DBDataBuilder;
		$obj->build(['ID'],
		            ['T' => ['Table' => 'Set']],
		            [],
		            'T');
	}

	/**
	 * @covers Evoke\Model\Data\DBDataBuilder::build
	 * @covers Evoke\Model\Data\DBDataBuilder::buildData
	 * @covers Evoke\Model\Data\DBDataBuilder::fillMetadataCache
	 * @expectedException        InvalidArgumentException
	 * @expectedExceptionMessage Joins must have Parent and Table.
	 */
	public function testMissingJoinTable()
	{
		$obj = new DBDataBuilder;
		$obj->build(['ID'],
		            ['T' => ['Parent' => 'Set']],
		            [],
		            'T');
	}
}
// EOF