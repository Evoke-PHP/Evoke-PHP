<?php
namespace Evoke_Test\Model;

use Evoke\Model\Factory,
	PHPUnit_Framework_TestCase;

class FactoryTest extends PHPUnit_Framework_TestCase
{ 
	/**
	 * Test the construction of a good object.
	 *
	 * @covers Evoke\Model\Factory::__construct
	 */
	public function test__constructGood()
	{
		$modelFactory = new Factory(
			$this->getMockBuilder('Evoke\Persistence\DB\SQL')
			->disableOriginalConstructor()
			->getMock());
		
		$this->assertInstanceOf('Evoke\Model\Factory', $modelFactory);
		
		return $modelFactory;
	}

	/**
	 * Test the building of simple data.
	 *
	 * @covers  Evoke\Model\Factory::buildData
	 * @depends test__constructGood
	 */
	public function testBuildDataSimple(Factory $modelFactory)
	{
		$this->assertInstanceOf('Evoke\Model\Data\Data',
		                        $modelFactory->buildData());		                        
	}

	/**
	 * Test that a specific data type can be built.
	 *
	 * @covers  Evoke\Model\Factory::buildData
	 * @depends test__constructGood
	 */
	public function testBuildDataSpecific(Factory $modelFactory)
	{
		$this->assertInstanceOf(
			'Evoke\Model\Data\Menu',
			$modelFactory->buildData('', array(), 'Evoke\Model\Data\Menu'));
	}

	/**
	 * Test that different data types can be built together using the data joins
	 * strings.
	 *
	 * @covers  Evoke\Model\Factory::buildData
	 * @depends test__constructGood
	 */
	public function testBuildDataComplex(Factory $modelFactory)
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
		$complexData = $modelFactory->buildData(
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