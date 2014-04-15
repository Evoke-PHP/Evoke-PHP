<?php
namespace Evoke_Test\Model\Mapper\DB;

use Evoke\Model\Mapper\DB\Table,
	PHPUnit_Framework_TestCase;

class PDOMock extends \PDO { public function __construct() {} }
class PDOStatementMock extends \PDOStatement { public function __construct() {} }

class TableTest extends PHPUnit_Framework_TestCase
{
	/******************/
	/* Data Providers */
	/******************/

	/*********/
	/* Tests */
	/*********/

	/**
	 * @covers Evoke\Model\Mapper\DB\Table::__construct
	 */
	public function testConstruct()
	{
		$obj = new Table($this->getMock('PDOMock'));
		$this->assertInstanceOf('Evoke\Model\Mapper\DB\Table', $obj);
	}
	
	/**
	 * @covers Evoke\Model\Mapper\DB\Table::create
	 * @covers Evoke\Model\Mapper\DB\Table::placeholdersKeyed
	 */
	public function testCreateGood()
	{
		$record = ['F1' => 1, 'F2' =>'20202', 'F3' => 333];
		$stmt = $this->getMock('PDOStatementMock', ['execute']);
		$stmt
			->expects($this->once())
			->method('execute')
			->with($record);
		
		$pdo = $this->getMock('PDOMock', ['prepare']);
		$pdo->expects($this->once())
			->method('prepare')
			->with('INSERT TNAME SET F1=?,F2=?,F3=?')
			->will($this->returnValue($stmt));

		$obj = new Table($pdo, 'TNAME');
		$obj->create($record);
	}

	/**
	 * @covers Evoke\Model\Mapper\DB\Table::create
	 * @covers Evoke\Model\Mapper\DB\Table::setTable
	 */
	public function testCreateGoodAfterSetTable()
	{
		$record = ['F1' => 1, 'F2' =>'20202', 'F3' => 333];
		$stmt = $this->getMock('PDOStatementMock', ['execute']);
		$stmt
			->expects($this->once())
			->method('execute')
			->with($record);
		
		$pdo = $this->getMock('PDOMock', ['prepare']);
		$pdo->expects($this->once())
			->method('prepare')
			->with('INSERT NEWTABLE SET F1=?,F2=?,F3=?')
			->will($this->returnValue($stmt));

		$obj = new Table($pdo, 'TNAME');
		$obj->setTable('NEWTABLE');
		$obj->create($record);
	}

	/**
	 * @covers Evoke\Model\Mapper\DB\Table::createMultiple
	 */
	public function testCreateMultipleGood()
	{
		$data = [['F1' => 0, 'F2' => '20202', 'F3' => 3],
		         ['F1' => 1, 'F2' => '21212', 'F3' => 33],
		         ['F1' => 2, 'F2' => '22222', 'F3' => 333]];
		$sIndex = 0;
		$stmt = $this->getMock('PDOStatementMock', ['execute']);

		for ($i = 0; $i < count($data); $i++)
		{
			$stmt
				->expects($this->at($sIndex++))
				->method('execute')
				->with($data[$i]);
		}
		
		$pdo = $this->getMock('PDOMock', ['prepare']);
		$pdo->expects($this->once())
			->method('prepare')
			->with('INSERT TNAME SET F1=?,F2=?,F3=?')
			->will($this->returnValue($stmt));

		$obj = new Table($pdo, 'TNAME');
		$obj->createMultiple($data);
	}

	/**
	 * @covers Evoke\Model\Mapper\DB\Table::delete
	 */
	public function testDeleteGoodNoLimit()
	{
		$conditions = ['ID' => 5];

		$stmt = $this->getMock('PDOStatementMock', ['execute']);
		$stmt
			->expects($this->once())
			->method('execute')
			->with($conditions);

		$pdo = $this->getMock('PDOMock', ['prepare']);
		$pdo->expects($this->once())
			->method('prepare')
			->with('DELETE FROM DT WHERE ID=?')
			->will($this->returnValue($stmt));
		
		$obj = new Table($pdo, 'DT');
		$obj->delete($conditions);
	}
	
	/**
	 * @covers Evoke\Model\Mapper\DB\Table::delete
	 */
	public function testDeleteGoodWithLimit()
	{
		$conditions = ['ID' => 5];
		$limit = 4;

		$stmt = $this->getMock('PDOStatementMock', ['execute']);
		$stmt
			->expects($this->once())
			->method('execute')
			->with($conditions);

		$pdo = $this->getMock('PDOMock', ['prepare']);
		$pdo->expects($this->once())
			->method('prepare')
			->with('DELETE FROM DT WHERE ID=? LIMIT 4')
			->will($this->returnValue($stmt));
		
		$obj = new Table($pdo, 'DT');
		$obj->delete($conditions, $limit);
	}

	/**
	 * @covers Evoke\Model\Mapper\DB\Table::read
	 */
	public function testReadGoodBasic()
	{
		$readData = [['F1' => 1, 'F2' => 2, 'F3' => 3],
		             ['F1' => 4, 'F2' => 5, 'F3' => 6]];
		$stmt = $this->getMock('PDOStatementMock', ['execute', 'fetchAll']);
		$stmt
			->expects($this->at(0))
			->method('execute')
			->with();
		$stmt
			->expects($this->once())
			->method('fetchAll')
			->with(\PDO::FETCH_NAMED)
			->will($this->returnValue($readData));

		$pdo = $this->getMock('PDOMock', ['prepare']);
		$pdo->expects($this->once())
			->method('prepare')
			->with('SELECT F1,F2,F3 FROM ST')
			->will($this->returnValue($stmt));
		
		$obj = new Table($pdo, 'ST');

		$this->assertSame($readData, $obj->read(['F1', 'F2', 'F3']));
	}

	/**
	 * @covers Evoke\Model\Mapper\DB\Table::read
	 */
	public function testReadGoodFull()
	{
		$readData = [['F1' => 1, 'F2' => 2, 'F3' => 3],
		             ['F1' => 4, 'F2' => 5, 'F3' => 6]];
		$stmt = $this->getMock('PDOStatementMock', ['execute', 'fetchAll']);
		$stmt
			->expects($this->at(0))
			->method('execute')
			->with();
		$stmt
			->expects($this->once())
			->method('fetchAll')
			->with(\PDO::FETCH_NAMED)
			->will($this->returnValue([$readData[0]]));

		$pdo = $this->getMock('PDOMock', ['prepare']);
		$pdo->expects($this->once())
			->method('prepare')
			->with('SELECT F1,F2,F3 FROM ST WHERE F2=? AND F4=? ORDER BY F4 DESC LIMIT 3')
			->will($this->returnValue($stmt));
		
		$obj = new Table($pdo, 'ST');
		$obj->read(['F1', 'F2', 'F3'],
		           ['F2' => 2, 'F4' => 4],
		           'F4 DESC',
		           3);
	}

	/**
	 * @covers Evoke\Model\Mapper\DB\Table::update
	 */
	public function testUpdateGood()
	{
		$stmt = $this->getMock('PDOStatementMock', ['execute']);
		$stmt
			->expects($this->once())
			->method('execute')
			->with(['Foo', 1]);			
		
		$pdo = $this->getMock('PDOMock', ['prepare']);
		$pdo->expects($this->once())
			->method('prepare')
			->with('UPDATE UT SET Text=? WHERE ID=?')
			->will($this->returnValue($stmt));
		
		$obj = new Table($pdo, 'UT');
		$obj->update(['ID' => 1],
		             ['Text' => 'Foo']);
	}

	/**
	 * @covers Evoke\Model\Mapper\DB\Table::update
	 */
	public function testUpdateGoodWithLimit()
	{
		$stmt = $this->getMock('PDOStatementMock', ['execute']);
		$stmt
			->expects($this->once())
			->method('execute')
			->with(['Foo', 1]);			
		
		$pdo = $this->getMock('PDOMock', ['prepare']);
		$pdo->expects($this->once())
			->method('prepare')
			->with('UPDATE UT SET Text=? WHERE ID=? LIMIT 2')
			->will($this->returnValue($stmt));
		
		$obj = new Table($pdo, 'UT');
		$obj->update(['ID' => 1],
		             ['Text' => 'Foo'],
		             2);
	}
}
// EOF