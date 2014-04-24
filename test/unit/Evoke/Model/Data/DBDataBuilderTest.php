<?php
namespace Evoke_Test\Model\Data;

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
		                     'T2');

		$mlaMetadata3 = new DB(['ID', 'N3'],
		                       [],
		                       ['ID'],
		                       'T3');
		$mlaMetadata2 = new DB(['ID', 'N2'],
		                       ['N3=T3_T_ID' => $mlaMetadata3],
		                       ['ID'],
		                       'T2');

		return [
			'Single_Table'   => [
				'Expected'             => new Data(
					new DB(['ID', 'Name'],
					       [],
					       ['ID'],
					       'Single'),
					[]),
				'Fields'               => ['Single' => ['ID', 'Name']],
				'Joins'                => [],
				'Primary_Keys'         => ['Single' => ['ID']],
				'Table_Name'           => 'Single'],
			'Multiple_Table' => [
				'Expected'             => new Data(
					new DB(['ID', 'N1'],
					       ['N2=T2_T_ID' => $t2Metadata],
					       ['ID'],
					       'T1'),
					['N2=T2_T_ID' => new Data($t2Metadata, [])]),
				'Fields'               => ['T1' => ['ID', 'N1'],
				                           'T2' => ['ID', 'N2']],
				'Joins'                => ['T1' => ['N2=T2_T_ID']],
				'Primary_Keys'         => ['T1' => ['ID'],
				                           'T2' => ['ID']],
				'Table_Name'           => 'T1'],
			'Multi_Level'    => [
				'Expected'             => new Data(
					new DB(['ID', 'N1'],
					       ['N2=T2_T_ID' => $mlaMetadata2],
					       ['ID'],
					       'T1'),
					['N2=T2_T_ID' => new Data(
                            $mlaMetadata2,
                            ['N3=T3_T_ID' => new Data($mlaMetadata3, [])])]),
				'Fields'               => ['T1' => ['ID', 'N1'],
				                           'T2' => ['ID', 'N2'],
				                           'T3' => ['ID', 'N3']],
				'Joins'                => ['T1' => ['N2=T2_T_ID'],
				                           'T2' => ['N3=T3_T_ID']],
				'Primary_Keys'         => ['T1' => ['ID'],
				                           'T2' => ['ID'],
				                           'T3' => ['ID']],
				'Table_Name'           => 'T1']];
	}
	
	/*********/
	/* Tests */
	/*********/

	/**
	 * @covers       Evoke\Model\Data\DBDataBuilder
	 * @dataProvider providerBuild
     * @uses         Evoke\Model\Data\Metadata\DB::__construct
	 * @uses         Evoke\Model\Data\Data::__construct
	 */
	public function testBuild(Data  $expected,
	                          Array $fields,
	                          Array $joins,
	                          Array $primaryKeys,
	                          /* String */ $tableName,
	                          /* String */ $tableAlias = NULL)
	{
		$obj = new DBDataBuilder();
		$this->assertEquals($expected,
		                    $obj->build($fields,
		                                $joins,
		                                $primaryKeys,
		                                $tableName,
		                                $tableAlias));
	}

    /**
     * @covers Evoke\Model\Data\DBDataBuilder
     * @uses   Evoke\Model\Data\Metadata\DB::__construct
	 * @uses   Evoke\Model\Data\Data::__construct
     */
    public function testCreateWithDifferentSeparator()
    {
		$t2Metadata = new DB(['ID', 'N2'],
		                     [],
		                     ['ID'],
		                     'T2');

        $obj = new DBDataBuilder('*SEP*');
        $this->assertEquals(
            new Data(new DB(['ID', 'N1'],
                            ['N2=T2*SEP*ID' => $t2Metadata],
                            ['ID'],
                            'T1'),
                     ['N2=T2*SEP*ID' => new Data($t2Metadata, [])]),
            $obj->build(['T1' => ['ID', 'N1'], 'T2' => ['ID', 'N2']],
                        ['T1' => ['N2=T2*SEP*ID']],
                        ['T1' => ['ID'], 'T2' => ['ID']],
                        'T1'));
    }
    
	/**
	 * @covers Evoke\Model\Data\DBDataBuilder
	 * @expectedException        DomainException
	 * @expectedExceptionMessage Missing child table in join: T1=Missing
	 */
	public function testJoinMissingChildTable()
	{
		$obj = new DBDataBuilder;
		$obj->build(['ID'],
		            ['T' => ['T1=Missing']],
		            [],
		            'T');
	}
}
// EOF