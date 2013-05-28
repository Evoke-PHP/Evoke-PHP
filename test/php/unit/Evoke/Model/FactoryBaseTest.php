<?php
namespace Evoke_Test\Model;

use Evoke\Model\FactoryBase,
	PHPUnit_Framework_TestCase;

/**
 * Factory Shim
 *
 * A shim to expose the protected methods of the factory base as public so that
 * they can be tested for usability.
 */
class FactoryShim extends FactoryBase
{
	public function createData(
		/* String */ $tableName = '',
		Array        $dataJoins = array(),
		/* String */ $dataType  = 'Evoke\Model\Data\Data')
	{
        return parent::createData($tableName, $dataJoins, $dataType);
	}
	
	protected function createJoins(Array $joins, $tableName)
	{
        return parent::createJoins($joins, $tableName);
	}

	protected function createMapperDBMenu(/* String */ $menuName)
	{
        return parent::createMapperDBMenu($menuName);
	}

	protected function createMapperDBJoint(/* String */ $tableName,
	                                       Array        $joins,
	                                       Array        $select =  array())
	{
        return parent::createMapperDBJoint($tableName, $joins, $select);
	}

	protected function createMapperDBTable(/* String */ $tableName,
	                                       Array        $select = array())
	{
        return parent::createMapperDBTable($tableName, $select);
	}
	
	protected function createMapperDBTables(Array $extraTables   = array(),
	                                        Array $ignoredTables = array())
	{
        return parent::createMapperDBTables($extraTables, $ignoredTables);
	}
	
	protected function createMapperSession(Array $domain)
	{
        return parent::createMapperSession($domain);
	}

	protected function createRecordList(/* String */ $tableName,
	                                    Array        $joins = array())
	{
        return parent::createRecordList($tableName, $joins);
	}
}

class FactoryBaseTest extends PHPUnit_Framework_TestCase
{ 
	/**
	 * Test the construction of a good object.
	 *
	 * @covers Evoke\Model\FactoryBase::__construct
	 */
	public function test__constructGood()
	{
		$modelFactory = new FactoryShim(
			$this->getMockBuilder('Evoke\Persistence\DB\SQL')
			->disableOriginalConstructor()
			->getMock());
		
		$this->assertInstanceOf('Evoke\Model\FactoryBase', $modelFactory);
		
		return $modelFactory;
	}

	/**
	 * Test the creation of simple data.
	 *
	 * @covers  Evoke\Model\FactoryBase::createData
	 * @depends test__constructGood
	 */
	public function testCreateDataSimple(FactoryShim $modelFactory)
	{
		$this->assertInstanceOf('Evoke\Model\Data\Data',
		                        $modelFactory->createData());		                        
	}

	/**
	 * Test that a specific data type can be built.
	 *
	 * @covers  Evoke\Model\FactoryBase::createData
	 * @depends test__constructGood
	 */
	public function testCreateDataSpecific(FactoryShim $modelFactory)
	{
		$this->assertInstanceOf(
			'Evoke\Model\Data\Menu',
			$modelFactory->createData('', array(), 'Evoke\Model\Data\Menu'));
	}

	/**
	 * Test that different data types can be built together using the data joins
	 * strings.
	 *
	 * @covers  Evoke\Model\FactoryBase::createData
	 * @depends test__constructGood
	 */
	public function testCreateDataComplex(FactoryShim $modelFactory)
	{
		/**
		 * Complex Data
		 *
		 *  +----------+       +--------------+
		 *  | Test     |       | Menu         |      +-----------+
		 *  +----------+       +--------------+      | Menu_List |
		 *  | Menu_ID  |------>| ID           |      +-----------+
		 *  | Other_ID |--.    | Menu_List_ID |----->| List_ID   |
		 *  +----------+  |    +--------------+      +-----------+
		 *                |
		 *                |    +-------+
		 *                |    | Other |
		 *                |    +-------+
		 *                 `-->| ID    |
		 *                     +-------+
		 *
		 * Data Types
		 *   - Menu      = Evoke\Model\Data\Menu 
		 *   - Menu_List = Evoke\Model\Data\Data
		 *   - Other     = Evoke\Model\Data\Data
		 *   - Test      = Evoke\Model\Data\Data
		 */  
		$complexData = $modelFactory->createData(
			'Test',
			array('Menu' => 'Evoke\Model\Data\Menu.Menu_List_ID=Menu_List.ID',
			      'Test' => 'Menu_ID=Menu.ID,Other_ID=Other.ID'));

		// We need to set the data within the complex data to gain access to
		// the internal data for testing.
		$complexData->setData(
			array(array('Menu_ID'    => 1,
			            'Other_ID'   => 2,
			            'Joint_Data' => array(
				            'Menu_ID' => array(
					            array('ID'           => 1,
					                  'Menu_List_ID' => 3,
					                  'Joint_Data'   => array(
						                  'Menu_List_ID' => array(
							                  array('List_ID' => 3))))),
				            'Other_ID' => array(
					            array('ID' => 2))))));
		
		$this->assertInstanceOf('Evoke\Model\Data\Data', $complexData);

		$menuData     = $complexData->menu;
		$menuListData = $menuData->menuList;
		$otherData    = $complexData->other;

		$this->assertInstanceOf('Evoke\Model\Data\Menu', $menuData);
		$this->assertInstanceOf('Evoke\Model\Data\Data', $menuListData);
		$this->assertInstanceOf('Evoke\Model\Data\Data', $otherData);

		// Ensure we aren't matching because Menu data is a derived type of
		// Data.  The only data that should be created as a Menu is in the menu
		// table.
		$this->assertEquals('Evoke\Model\Data\Data', get_class($menuListData));
	}
}
// EOF