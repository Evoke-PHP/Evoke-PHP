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
	
}
// EOF