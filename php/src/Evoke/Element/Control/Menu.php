<?php
namespace Evoke\Element\Control;

class Menu extends \Evoke\Element\Base
{
	/** @property $Translator
	 *  Translator \object
	 */
	protected $Translator;

	public function __construct(Evoke\Iface\Translator $translator)
	{
		$this->translator = $translator;
	}

	/******************/
	/* Public Methods */
	/******************/

	/** Set the menu with the menu items.
	 *  @param menuItems \array The menu items.
	 *  \return \array The menu element data.
	 */
	public function set(Array $menuItems)
	{
		return parent::set(
			array('ul',
			      array(),
			      $this->buildMenu($menuItems)));
	}
   
	/*******************/
	/* Private Methods */
	/*******************/

	private function buildMenu($data, $level = 0)
	{
		$lang = $this->Translator->getLanguage();
		$menu = array();
      
		foreach ($data as $menuItem)
		{
			if (isset($menuItem['Children']))
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