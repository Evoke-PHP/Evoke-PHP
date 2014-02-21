<?php
/**
 * Menu Control View
 *
 * @package View\XHTML\Control
 */
namespace Evoke\View\XHTML;

use Evoke\Model\Data\TreeIface as Tree,
	Evoke\View\ViewIface,
	LogicException;

/**
 * Menu Control View
 *
 * @author    Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license   MIT
 * @package   View\XHTML\Control
 */
class Menu implements ViewIface
{
	/**
	 * Protected properties.
	 *
	 * @var Tree $tree Tree data.
	 */
	protected $tree;

	/******************/
	/* Public Methods */
	/******************/

	/**
	 * Get the view of the menu.
	 *
	 * @return mixed[] The view tree.
	 */
	public function get()
	{
		if (!isset($this->tree))
		{
			throw new LogicException('needs tree to be set.');
		}

		return array('div',
		             array('class' => 'Menu ' . $this->tree->get()),
		             array($this->getMenu($this->tree)));
	}

	protected function getMenu(TreeIface $node, $level)
	{
		return [];
		             
		/*
		while ($treeNode->hasChildren())foreach ($menus as $menu)
		{
			$menuElements[] = array(
				'ul',
				array('class' => 'Menu ' . $menu['Name']),
				$this->buildMenu($menu['Items'][0]['Children']));
		}

		return array('div',
		             array('class' => 'Menu ' . $this->tree->get()),
		             $menuElements);
		*/
	}

	/**
	 * Set the menu data.
	 *
	 * @param Tree Menu data.
	 */
	public function set(Tree $tree)
	{
		$this->tree = $tree;
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
					            $menuItem['Text']),
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
					            $menuItem['Text'])
						));
			}
		}
      
		return $menu;
	}
}
// EOF