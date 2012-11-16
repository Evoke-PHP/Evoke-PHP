<?php
namespace Evoke\View\Control;

use Evoke\Model\Data\Menu as DataMenu,
	Evoke\Model\Data\TranslationsIface,
	Evoke\View\ViewIface;

/**
 * Menu
 *
 * @author Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license MIT
 * @package View
 */
class Menu implements ViewIface
{
	/**
	 * Data object.
	 * @var Evoke\Model\Data\Menu
	 */
	protected $data;

	/**
	 * Class for the menu items.
	 *
	 * @var string
	 */
	protected $menuItemClass;
	
	/**
	 * Translations data.
	 * @var Evoke\Model\Data\TranslationsIface
	 */
	protected $translations;
	
	/**
	 * Construct a Menu object.
	 *
	 * @param Evoke\Model\Data\Menu              Menu Data.
	 * @param Evoke\Model\Data\TranslationsIface Translations.
	 * @param string                             Menu Item class
	 */
	public function __construct(DataMenu          $data,
	                            TranslationsIface $translations,
	                            /* String */      $menuItemClass = 'Menu_Item')
	{
		$this->data          = $data;
		$this->menuItemClass = $menuItemClass;
		$this->translations    = $translations;
	}
	
	/******************/
	/* Public Methods */
	/******************/

	/**
	 * Get the menu represented by the data.
	 *
	 * @param Parameters to the view.
	 *
	 * @return mixed[] The menu element data.
	 */
	public function get(Array $params = array())
	{
		$menus = $this->data->getMenu();
		$menusElements = array();
		
		foreach ($menus as $menu)
		{
			$menusElements[] = array(
				'ul',
				array('class' => 'Menu ' . $menu['Name']),
				$this->buildMenu($menu['Items'][0]['Children']));
		}

		return (count($menusElements) > 1) ?
			array('div', array('class' => 'Menus'), $menusElements) :
			reset($menusElements);
	}
   
	/*******************/
	/* Private Methods */
	/*******************/

	/**
	 * Build the menu elements.
	 *
	 * @return mixed[] The menu elements.
	 */
	private function buildMenu($data, $level = 0)
	{
		$lang = $this->translations->getLanguage();
		$menu = array();

		foreach ($data as $menuItem)
		{
			if (!empty($menuItem['Children']))
			{
				$menu[] = array(
					'li',
					array('class' => $this->menuItemClass . ' Level_' . $level),
					array(array('a',
					            array('href' => $menuItem['Href']),
					            $menuItem['Text_' . $lang]),
					      array('ul',
					            array(),
					            $this->buildMenu(
						            $menuItem['Children'], ++$level))));
			}
			else
			{
				$menu[] = array(
					'li',
					array('class' => $this->menuItemClass . ' Level_' . $level),
					array(array('a',
					            array('href' => $menuItem['Href']),
					            $menuItem['Text_' . $lang])));
			}
		}
      
		return $menu;
	}
}
// EOF