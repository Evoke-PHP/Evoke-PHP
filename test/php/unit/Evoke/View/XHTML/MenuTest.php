<?php
namespace Evoke_Test\View\XHTML;

use Evoke\Model\Data\MenuIface,
	Evoke\View\XHTML\Menu,
	PHPUnit_Framework_TestCase;

class MenuTest extends PHPUnit_Framework_TestCase
{
	/*********/
	/* Tests */
	/*********/

	/**
	 * Requires menu data but not given any.
	 *
	 * @covers Evoke\View\XHTML\Menu::get
	 * @expectedException LogicException
	 */
	public function testRequiresDataMenuNoneGiven()
	{
		$obj = new Menu;
		$obj->get();
	}

	/**
	 * Requires menu data but given an incorrect type.
	 *
	 * @covers            Evoke\View\XHTML\Menu::get
	 * @covers            Evoke\View\XHTML\Menu::set
	 * @expectedException ErrorException
	 */
	public function testRequiresDataMenu()
	{
		$obj = new Menu;
		set_error_handler(function () {
				throw new \ErrorException("Expected");
			});

		try
		{
			$obj->set(34);
		}
		catch (\ErrorException $e)
		{
			restore_error_handler();
			throw $e;
		}
		
		restore_error_handler();
	}
	
	/**
	 * Empty Menu.
	 *
	 * @covers Evoke\View\XHTML\Menu::get
	 * @covers Evoke\View\XHTML\Menu::set
	 */
	public function testGetEmptyMenu()
	{
		$mockTree = $this->getMock('Evoke\Model\Data\TreeIface');
		$mockTree
			->expects($this->at(0))
			->method('getValue')
			->with()
			->will($this->returnValue('Empty_Menu'));
		$mockTree
			->expects($this->at($tIndex++))
			->method('hasChildren')
			->with()
			->will($this->returnValue(false));
		
		$obj = new Menu;
		$obj->set($mockTree);
		$this->assertSame(['div', ['class' => 'Empty_Menu'], []],
		                  $obj->get());
	}
	
	/**
	 * Single Level Menu.
	 *
	 * @covers Evoke\View\XHTML\Menu::get
	 * @covers Evoke\View\XHTML\Menu::set
	 * @covers Evoke\View\XHTML\Menu::buildMenu
	 */
	public function testGetSingleLevelMenu()
	{
		/*
		$mockData = $this->getMock('Evoke\Model\Data\TreeIface');
		$mockData
			->expects($this->at(0))
			->method('getMenu')
			->with()
			->will($this->returnValue(
				       [['Name' => 'SL_Name',
				         'Items' => [
					         ['Children' => [
							         ['Href' => 'SL_Href',
							          'Text' => 'SL_Text']]]]]]));
		
		$obj = new Menu;
		$obj->set($mockData);
		$this->assertSame(
			['div',
			 ['class' => 'Menus'],
			 [['ul',
			   ['class' => 'Menu SL_Name'],
			   [['li',
			     ['class' => 'Menu_Item Level_0'],
			     [['a',
			       ['href' => 'SL_Href'],
			       'SL_Text']]]]]]],			                     
			$obj->get());
		*/
	}

	/**
	 * Multi Level Menu.
	 *
	 * @covers Evoke\View\XHTML\Menu::get
	 * @covers Evoke\View\XHTML\Menu::set
	 * @covers Evoke\View\XHTML\Menu::buildMenu
	 */
	public function testGetMultiLevelMenu()
	{
		/*
		$mockData = $this->getMock('Evoke\Model\Data\TreeIface');
		$mockData
			->expects($this->at(0))
			->method('getMenu')
			->with()
			->will($this->returnValue(
				       [['Name' => 'ML_Name',
				         'Items' => [
					         ['Children' => [
							         ['Href' => 'ML_Href',
							          'Text' => 'ML_Text',
							          'Children' => [
								          ['Href' => 'HSub1',
								           'Text' => 'TSub1']]]]]]]]));
		
		$obj = new Menu;
		$obj->setData($mockData);
		$this->assertSame(
			['div',
			 ['class' => 'Menus'],
			 [['ul',
			   ['class' => 'Menu ML_Name'],
			   [['li',
			     ['class' => 'Menu_Item Level_0'],
			     [['a',
			       ['href' => 'ML_Href'],
			       'ML_Text'],
			      ['ul',
			       [],
			       [['li',
			         ['class' => 'Menu_Item Level_1'],
			         [['a',
			           ['href' => 'HSub1'],
			           'TSub1']]]]]]]]]]],
			$obj->get());
		*/
	}	
}
// EOF