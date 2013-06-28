<?php
/**
 * Menu Control View
 *
 * @package View\Control
 */
namespace Evoke\View\Control;

use Evoke\Model\Data\Menu as DataMenu,
	Evoke\View\ViewIface,
	LogicException;

/**
 * Menu Control View
 *
 * @author    Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license   MIT
 * @package   View\Control
 */
class Menu implements ViewIface
{
	/**
	 * Protected properties.
	 *
	 * @var DataMenu $data     Menu data.
	 * @var string   $language Language of the menu.
	 */
	protected $data, $language;

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
			throw new LogicException('needs data as Data\Menu');
		}
		
		if (!isset($this->language))
		{
			throw new LogicException('needs language.');
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

	/**
	 * Set the menu data.
	 *
	 * @param DataMenu Menu data.
	 */
	public function setData(DataMenu $dataMenu)
	{
		$this->data = $dataMenu;
	}

	/**
	 * Set the language of the menu.
	 *
	 * @param string Language of the menu.
	 */
	public function setLanguage(/* String */ $language)
	{
		$this->language = (string) $language;
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
					            $menuItem['Text_' . $this->params['Language']]),
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
					            $menuItem['Text_' . $this->params['Language']])
						));
			}
		}
      
		return $menu;
	}
}
// EOF