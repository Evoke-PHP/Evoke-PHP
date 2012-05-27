<?php
namespace Evoke\Element\Control;

class Menu extends \Evoke\Element\Translator
{
	/******************/
	/* Public Methods */
	/******************/

	/** Set the menu with the menu items.
	 *  @param menuItems \array The menu items.
	 *  \return \array The menu element data.
	 */
	public function set(Array $menu)
	{
		return parent::set(
			array('ul',
			      array('class' => 'Menu ' . $menu['Name']),
			      $this->buildMenu($menu['Items'][0]['Children'])));
	}
   
	/*******************/
	/* Private Methods */
	/*******************/

	private function buildMenu($data, $level = 0)
	{
		$lang = $this->translator->getLanguage();
		$menu = array();

		foreach ($data as $menuItem)
		{
			if (!empty($menuItem['Children']))
			{
				$menu[] = array(
					'li',
					array('class' => 'Level_' . $level),
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
					array('class' => 'Level_' . $level),
					array(array('a',
					            array('href' => $menuItem['Href']),
					            $menuItem['Text_' . $lang])));
			}
		}
      
		return $menu;
	}
}
// EOF