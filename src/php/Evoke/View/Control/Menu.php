<?php
namespace Evoke\View\Control;

use Evoke\Model\Data\Menu as DataMenu,
	Evoke\View\View,
	InvalidArgumentException;

/**
 * Menu
 *
 * @author Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license MIT
 * @package View
 */
class Menu extends View
{
	/**
	 * Language
	 * @var string
	 */
	protected $language;
	
	/**
	 * Construct a Menu object.
	 *
	 * @param string Language.
	 */
	public function __construct(/* String */ $language)
	{
		$this->language = $language;
	}
	
	/******************/
	/* Public Methods */
	/******************/

	/**
	 * Get the view of the menu.
	 *
	 * @return mixed[] The view data.
	 */
	public function get()
	{
		if (!$this->data instanceof DataMenu)
		{
			throw new InvalidArgumentException('needs data as Data\Menu');
		}
		
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
	 * @param mixed[] The data for the menu items.
	 * @param int     The current level of the menu items.
	 * @return mixed[] The menu elements.
	 */
	private function buildMenu(Array $data, $level = 0)
	{
		$menu = array();

		foreach ($data as $menuItem)
		{
			if (!empty($menuItem['Children']))
			{
				$menu[] = array(
					'li',
					array('class' => 'Menu_Item Level_' . $level),
					array(array('a',
					            array('href' => $menuItem['Href']),
					            $menuItem['Text_' . $this->language]),
					      array('ul',
					            array(),
					            $this->buildMenu(
						            $menuItem['Children'], ++$level))));
			}
			else
			{
				$menu[] = array(
					'li',
					array('class' => 'Menu_Item Level_' . $level),
					array(array('a',
					            array('href' => $menuItem['Href']),
					            $menuItem['Text_' . $this->language])));
			}
		}
      
		return $menu;
	}
}
// EOF