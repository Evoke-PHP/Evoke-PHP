<?php
namespace Evoke_Test\Persistence\DB;

use Evoke\Persistence\DB\SQL,
    PHPUnit_Framework_TestCase;

/**
 *  @covers Evoke\Persistence\DB\SQL
 */
class SQLTest extends PHPUnit_Framework_TestCase
{
	/******************/
	/* Data Providers */
	/******************/

	public function providerPassthrough()
	{
		return [
			'Error_Code'            => ['Method' => 'errorCode',
			                            'Params' => [],
			                            'Retval' => 123],
			'Error_Info'            => ['Method' => 'errorInfo',
			                            'Params' => [],
			                            'Retval' => 'DB Error'],
			'Exec'                  => ['Method' => 'exec',
			                            'Params' => ['statement'],
			                            'Retval' => 5],
			'Get_Attribute'         => ['Method' => 'getAttribute',
			                            'Params' => ['attrib'],
			                            'Retval' => 'attrib_val'],
			'Get_Available_Drivers' => ['Method' => 'getAvailableDrivers',
			                            'Params' => [],
			                            'Retval' => 'drivers'],
			'Last_Insert_ID'        => ['Method' => 'lastInsertId',
			                            'Params' => ['name'],
			                            'Retval' => 22],
			'Quote'                 => ['Method' => 'quote',
			                            'Params' => ['str'],
			                            'Retval' => 'quote this'],
			'Set_Attribute'         => ['Method' => 'setAttribute',
			                            'Params' => ['attrib', 'val'],
			                            'Retval' => TRUE]
			];
	}

	public function providerQuery()
	{
		return [
			['Statement_Class'    => 'PDOStatement',
			 'Named_Placeholders' => true,
			 'Query_String' => 'SELECT :P1,:P2 FROM Table']
			];
	}
	
	public function providerSelect()
	{
		return[
			'Table_All'    => [
				'Params'         => [['Table'], '*'],
				'Prepare_String' => 'SELECT * FROM Table',
				'Results'        => [['Table_Field_1' => 1,
				                      'Table_Field_2' => 2],
				                     ['Table_Field_1' => 'a',
				                      'Table_Field_2' => 'b']]
				],
			'Placeholders' => [
				'Params'         => [['T1', 'T2'],
				                     ['F1', 'F2'],
				                     ['F3' => 3,
				                      'F4' => 4],
				                     ['F5' => 'ASC',
				                      'F6' => 'DESC'],
				                     9,
				                     TRUE],
				'Prepare_String' => 'SELECT DISTINCT F1,F2 FROM T1,T2 WHERE ' .
				'F3=? AND F4=? ORDER BY F5 ASC,F6 DESC LIMIT 9',
				'Results'        => [['F1' => 1, 'F2' => 2],
				                     ['F1' => 11, 'F2' => 22]]
					
				]
			];
	}

	public function providerUpdateGood()
	{
		return[
			'All_Params' => [
				'Execution_Parameters' => [['Table'], '*'],
				'Params'               => [['T1', 'T2'],
				                           ['T1.F1' => 't1f1',
				                            'T2.F1' => 't2f1'],
				                           ['T1.F1' => 1,
				                            'T1.F2' => 3],
				                           9],
				'Prepare_String'       =>
				'UPDATE T1,T2 SET T1.F1=?,T2.F1=? WHERE T1.F1=? AND T1.F2=? LIMIT 9']
			];
	}
	
	/*********/
	/* Tests */
	/*********/

	/**
	 * @covers Evoke\Persistence\DB\SQL::__construct
	 */
	public function testCreateObject()
	{
		$this->assertInstanceOf(
			'Evoke\Persistence\DB\SQL',
			new SQL($this->getMock('Evoke\Persistence\DB\DBIface')));
	}

	/**
	 * @dataProvider providerPassthrough
	 */
	public function testPassthrough($method, Array $params = [], $retVal = NULL)
	{
		$db = $this->getMock('Evoke\Persistence\DB\SQL');
		$db
			->expects($this->at(0))
			->method($method)
			->will($this->returnValue($retVal));
		
		$object = new SQL($db);
		$this->assertEquals(
			$retVal, call_user_func_array([$object, $method], $params));
	}

	/**
	 * Ensure that a query is executed an returns the specified statement class.
	 *
	 * covers        Evoke\Persistence\DB\SQL::prepare
	 * @dataProvider providerQuery
	 */
	public function testQuery($statementClass, $namedPlaceholders, $queryString)
	{
		$statementObject = $this->getMock($statementClass);
		$dbIndex = 0;
		$db = $this->getMock('Evoke\Persistence\DB\SQL');
		$db
			->expects($this->at($dbIndex++))
			->method('setAttribute')
			->with(\PDO::ATTR_STATEMENT_CLASS,
			       [$statementClass, [$namedPlaceholders]]);
		
		$db->expects($this->at($dbIndex++))
			->method('query')
			->with($queryString)
			->will($this->returnValue($statementObject));
		
		$object = new SQL($db, $statementClass);
		$this->assertInstanceOf($statementClass, $object->query($queryString));
	}
	
	/**
	 * @dataProvider providerSelect
	 */
	public function testSelect($params, $prepareString, $results)
	{		
		$conditions = $params[2] ?: [];
		$order = $params[3] ?: [];

		if (!is_array($conditions))
		{
			$conditions = [$conditions];
		}

		if (!is_array($order))
		{
			$order = [$order];
		}
		
		$executionParameters = array_merge($conditions, $order);

		$statementMock = $this->getMock('PDOStatement');
		$statementMock
			->expects($this->at(0))
			->method('execute')
			->with($executionParameters);
		$statementMock
			->expects($this->at(1))
			->method('fetchAll')
			->will($this->returnValue($results));
			
		$db = $this->getMock('Evoke\Persistence\DB\DBIface');
		$db
			->expects($this->once())
			->method('prepare')
			->with($prepareString)
			->will($this->returnValue($statementMock));
		$object = new SQL($db);
		
		$actualResults = call_user_func_array([$object, 'select'], $params);

		$this->assertEquals($results, $actualResults);
	}

	/**
	 * @covers            Evoke\Persistence\DB\SQL::select
	 * @expectedException Evoke\Message\Exception\DB
	 */
	public function testSelectException()
	{
		$db = $this->getMock('Evoke\Persistence\DB\DBIface');
		$db
			->expects($this->once())
			->method('prepare')
			->with('SELECT Field FROM Table')
			->will($this->throwException(new \Exception));
		$object = new SQL($db);
		$object->select('Table', 'Field');
	}

	/**
	 * Ensure that we can get a single value.
	 *
	 * @todo Write test.
	 */
	public function testSelectSingleValueGood()
	{
		$singleValue = 987;
		$statementMock = $this->getMock('PDOStatement');
		$statementMock
			->expects($this->at(0))
			->method('execute')
			->with([]);
		$statementMock
			->expects($this->at(1))
			->method('fetchColumn')
			->will($this->returnValue($singleValue));
		
		$db = $this->getMock('Evoke\Persistence\DB\DBIface');
		$db
			->expects($this->once())
			->method('prepare')
			->with('SELECT F1 FROM T LIMIT 1')
			->will($this->returnValue($statementMock));
		$object = new SQL($db);
		$this->assertEquals($singleValue,
		                    $object->selectSingleValue('T', 'F1', []));
	}
	
	/**
	 * @covers Evoke\Persistence\DB\SQL::commit
	 */
	public function testTransactionCommitNormal()
	{
		$db =$this->getMock('Evoke\Persistence\DB\DBIface');
		$db
			->expects($this->once())
			->method('commit')
			->will($this->returnValue(true));
		
		$object = new SQL($db);
		$object->beginTransaction();
		
		$this->assertTrue($object->inTransaction());
		$this->assertTrue($object->commit());
		$this->assertFalse($object->inTransaction());
	}

	/**
	 * @covers Evoke\Persistence\DB\SQL::commit
	 * @expectedException Evoke\Message\Exception\DB
	 */
	public function testTransactionCommitOutsideOfTransaction()
	{
		$db =$this->getMock('Evoke\Persistence\DB\DBIface');

		$object = new SQL($db);
		$this->assertFalse($object->inTransaction(), 'Not in a transaction');
		$object->commit();
	}

	/**
	 * Ensure that rolling back a transaction calls rollBack and modifies
	 * inTransaction appropriately.
	 *
	 * @covers Evoke\Persistence\DB\SQL::rollBack
	 */
	public function testTransactionRollBackNormal()
	{
		$db = $this->getMock('Evoke\Persistence\DB\DBIface');
		$db->expects($this->once())
			->method('rollBack');
		
		$object = new SQL($db);
		$object->beginTransaction();
		
		$this->assertTrue($object->inTransaction());
		$object->rollBack();
		$this->assertFalse($object->inTransaction());
	}

	/**
	 * Ensure that a rollback outside of a transaction throws an exception.
	 *
	 * @expectedException Evoke\Message\Exception\DB
	 */
	public function testTransactionRollBackOutsideOfTransaction()
	{
		$object = new SQL($this->getMock('Evoke\Persistence\DB\DBIface'));
		
		$this->assertFalse($object->inTransaction());
		$object->rollBack();
	}
	
	/**
	 * @covers Evoke\Persistence\DB\SQL::beginTransaction
	 * @covers Evoke\Persistence\DB\SQL::inTransaction
	 */
	public function testTransactionStartNormal()
	{
		$db =$this->getMock('Evoke\Persistence\DB\DBIface');
		$db
			->expects($this->at(0))
			->method('beginTransaction')
			->will($this->returnValue(true));
		
		$object = new SQL($db);
		
		$this->assertFalse($object->inTransaction());
		$this->assertTrue($object->beginTransaction());
		$this->assertTrue($object->inTransaction());
	}

	/**
	 * @covers            Evoke\Persistence\DB\SQL::beginTransaction
	 * @expectedException Evoke\Message\Exception\DB
	 */
	public function testTransactionStartTwice()
	{
		$db =$this->getMock('Evoke\Persistence\DB\DBIface');
		$db
			->expects($this->at(0))
			->method('beginTransaction')
			->will($this->returnValue(true));
		
		$object = new SQL($db);
		$object->beginTransaction();

		$this->assertTrue(TRUE, 'No exception yet.');
		$object->beginTransaction();
	}

	/**
	 * Ensure that a database update is prepared and executed as expected.
	 *
	 * @dataProvider providerUpdateGood
	 */
	public function testUpdateGood(
		$executionParameters, $params, $prepareString)
	{
		$setValues = $params[1] ?: [];
		$conditions = $params[2] ?: [];

		if (!is_array($setValues))
		{
			$setValues = [$setValues];
		}

		if (!is_array($conditions))
		{
			$conditions = [$conditions];
		}
		
		$executionParameters = array_merge(array_values($setValues),
		                                   array_values($conditions));

		$statementMock = $this->getMock('PDOStatement');
		$statementMock
			->expects($this->at(0))
			->method('execute')
			->with($executionParameters)
			->will($this->returnValue(TRUE));
			
		$db = $this->getMock('Evoke\Persistence\DB\DBIface');
		$db
			->expects($this->once())
			->method('prepare')
			->with($prepareString)
			->will($this->returnValue($statementMock));
		$object = new SQL($db);
		
		call_user_func_array([$object, 'update'], $params);
	}

	/**
	 * Ensure that a failed execution throws a database exception.
	 *
	 * @expectedException Evoke\Message\Exception\DB
	 */
	public function testUpdateThrowsForExecution()
	{
		$statementMock = $this->getMock('PDOStatement');
		$statementMock
			->expects($this->once())
			->method('execute')
			->will($this->returnValue(FALSE));
		
		$db = $this->getMock('Evoke\Persistence\DB\DBIface');
		$db
			->expects($this->once())
			->method('prepare')
			->will($this->returnValue($statementMock));

		$object = new SQL($db);
		$object->update('table', ['field' => 'one']);
	}

	/**
	 * Ensure that a bad prepare throws a database exception.
	 *
	 * @expectedException Evoke\Message\Exception\DB
	 */
	public function testUpdateThrowsForPrepare()
	{
		$db = $this->getMock('Evoke\Persistence\DB\DBIface');
		$db
			->expects($this->once())
			->method('prepare')
			->will($this->throwException(new \Exception));
		$object = new SQL($db);

		$object->update('table', ['field' => 'one']);		
	}
}
// EOF