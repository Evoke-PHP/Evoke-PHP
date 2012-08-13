<?php
namespace Evoke_Test\Persistence\DB\Table;

use Evoke\Persistence\DB\Table\Info,
	PHPUnit_Framework_TestCase;

/**
 *  @covers Evoke\Persistence\DB\Table\Info
 */
class InfoTest extends PHPUnit_Framework_TestCase
{ 
	/**
	 * Ensure that a table with good information can provide information.
	 *
	 * @covers       Evoke\Persistence\DB\Table\Info::__construct
	 * @dataProvider providerGood
	 */	  
	public function test__constructGood($tableName, $createTableString, $fields)
	{
		$sqlIndex = 0;
		
		$sqlMock = 	$this
			->getMockBuilder('Evoke\Persistence\DB\SQL')
			->disableOriginalConstructor()
			->getMock();
				
		$sqlMock->expects($this->at($sqlIndex++))
			->method('getSingleValue')
			->with('SHOW CREATE TABLE ' . $tableName)
			->will($this->returnValue($createTableString));

		$sqlMock->expects($this->at($sqlIndex++))
			->method('getAssoc')
			->with('DESCRIBE ' . $tableName)
			->will($this->returnValue($fields));
		
		$obj = new Info($sqlMock, $tableName);
		$this->assertInstanceOf('Evoke\Persistence\DB\Table\Info', $obj);
	}

	/******************/
	/* Data Providers */
	/******************/

	public function providerGood()
	{
		$tableName = 'Menu_List';
		$createTableString = 'CREATE TABLE `' . $tableName . '` (' . "\n" .
			'`ID` int(11) NOT NULL AUTO_INCREMENT,' . "\n" .
			'`Menu_ID` int(11) NOT NULL,' . "\n" .
			'`Href` varchar(255) NOT NULL,' . "\n" .
			'`Text_EN` varchar(100) NOT NULL,' . "\n" .
			'`Text_ES` varchar(100) NOT NULL,' . "\n" .
			'`Lft` int(11) NOT NULL,' . "\n" .
			'`Rgt` int(11) NOT NULL,' . "\n" .
			'PRIMARY KEY (`ID`),' . "\n" .
			'KEY `Menu_ID` (`Menu_ID`),' . "\n" .
			'CONSTRAINT `Menu_List_ibfk_1` FOREIGN KEY (`Menu_ID`)' .
			' REFERENCES `Menu` (`List_ID`)' . "\n" .
			') ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8';
		
		/*
		  +---------+--------------+------+-----+---------+----------------+
		  | Field   | Type         | Null | Key | Default | Extra          |
		  +---------+--------------+------+-----+---------+----------------+
		  | ID      | int(11)      | NO   | PRI | NULL    | auto_increment |
		  | Menu_ID | int(11)      | NO   | MUL | NULL    |                |
		  | Href    | varchar(255) | NO   |     | NULL    |                |
		  | Text_EN | varchar(100) | NO   |     | NULL    |                |
		  | Text_ES | varchar(100) | NO   |     | NULL    |                |
		  | Lft     | int(11)      | NO   |     | NULL    |                |
		  | Rgt     | int(11)      | NO   |     | NULL    |                |
		  +---------+--------------+------+-----+---------+----------------+
		*/
		$fields = array(
			array('Default' => NULL,
			      'Extra'   => 'auto_increment',
			      'Field'   => 'ID',
			      'Key'     => 'PRI',
			      'Null'    => 'NO',
			      'Type'    => 'int(11)'),
			array('Default' => NULL,
			      'Extra'   => '',
			      'Field'   => 'Menu_ID',
			      'Key'     => 'MUL',
			      'Null'    => 'NO',
			      'Type'    => 'int(11)'),
			array('Default' => NULL,
			      'Extra'   => '',
			      'Field'   => 'Href',
			      'Key'     => '',
			      'Null'    => 'NO',
			      'Type'    => 'varchar(255)'),
			array('Default' => NULL,
			      'Extra'   => '',
			      'Field'   => 'Text_EN',
			      'Key'     => '',
			      'Null'    => 'NO',
			      'Type'    => 'varchar(100)'),
			array('Default' => NULL,
			      'Extra'   => '',
			      'Field'   => 'Text_ES',
			      'Key'     => '',
			      'Null'    => 'NO',
			      'Type'    => 'varchar(100)'),
			array('Default' => NULL,
			      'Extra'   => '',
			      'Field'   => 'Lft',
			      'Key'     => '',
			      'Null'    => 'NO',
			      'Type'    => 'int(11)'),
			array('Default' => NULL,
			      'Extra'   => '',
			      'Field'   => 'Rgt',
			      'Key'     => '',
			      'Null'    => 'NO',
			      'Type'    => 'int(11)'));
		
		return array(array($tableName, $createTableString, $fields));
	}
}
// EOF