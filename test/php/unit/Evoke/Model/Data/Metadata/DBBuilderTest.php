<?php
namespace Evoke_Test\Model\Data\Metadata;

use Evoke\Model\Data\Metadata\DB,
	Evoke\Model\Data\Metadata\DBBuilder,
	PHPUnit_Framework_TestCase;

class DBBuilderTest extends PHPUnit_Framework_TestCase
{
	/******************/
	/* Data Providers */
	/******************/

	public function providerBuild()
	{
		return [
			'Single_Table'        => [
				'Expected'             => new DB(['ID', 'Name'],
				                                 [],
		                                          ['ID'],
		                                          'Single',
		                                          'Single'),
				'Fields'               => ['Single' => ['ID', 'Name']],
				'Joins'                => [],
				'Primary_Keys'         => ['Single' => ['ID']],
				'Table_Name'           => 'Single'],
			'Multiple_Table'      => [
				'Expected'             =>
				new DB(['ID', 'N1'],
				       ['N2' => new DB(['ID', 'N2'],
				                       [],
				                       ['ID'],
				                       'T2',
				                       'T2')],
				       ['ID'],
				       'T1',
				       'T1'),
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
				'Expected'             =>
				new DB(['ID', 'N1'],
				       ['N2' => new DB(['ID', 'N2'],
				                       ['N3' => new DB(['ID', 'N3'],
				                                       [],
				                                       ['ID'],
				                                       'T3Aliased',
				                                       'T3')],
				                       ['ID'],
				                       'T2',
				                       'T2')],
				       ['ID'],
				       'T1',
				       'T1'),
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
	 * @covers       Evoke\Model\Data\Metadata\DBBuilder::build
	 * @dataProvider providerBuild
	 */
	public function testBuild(DB    $expected,
	                          Array $fields,
	                          Array $joins,
	                          Array $primaryKeys,
	                          /* String */ $tableName,
	                          /* String */ $tableAlias = NULL)
	{
		$obj = new DBBuilder;
		$this->assertEquals($expected,
		                    $obj->build($fields,
		                                $joins,
		                                $primaryKeys,
		                                $tableName,
		                                $tableAlias));
	}

	/**
	 * @covers                   Evoke\Model\Data\Metadata\DBBuilder::build
	 * @expectedException        InvalidArgumentException
	 * @expectedExceptionMessage Joins must have Parent and Table.
	 */
	public function testMissingJoinParent()
	{
		$obj = new DBBuilder;
		$obj->build(['ID'],
		            ['T' => ['Table' => 'Set']],
		            [],
		            'T');
	}

	/**
	 * @covers                   Evoke\Model\Data\Metadata\DBBuilder::build
	 * @expectedException        InvalidArgumentException
	 * @expectedExceptionMessage Joins must have Parent and Table.
	 */
	public function testMissingJoinTable()
	{
		$obj = new DBBuilder;
		$obj->build(['ID'],
		            ['T' => ['Parent' => 'Set']],
		            [],
		            'T');
	}
}
// EOF