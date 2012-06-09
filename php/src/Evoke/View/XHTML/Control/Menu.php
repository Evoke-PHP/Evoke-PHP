<?php
namespace Evoke\View\XHTML\Control;

use Evoke\Model\Data\Menu as DataMenu,
	Evoke\Service\TranslatorIface,
	Evoke\View\ViewIface;

class Menu implements ViewIface
{
	/** @property data
	 *  @object Data
	 */
	protected $data;

	/** Construct a Menu object.
	 *  @param translator @object Translator.
	 *  @param data       @object Data for the menu.
	 */
	public function __construct(TranslatorIface $translator,
	                            DataMenu        $data)
	{
		parent::__construct($translator);
		
		$this->data = $data;
	}
	
	/******************/
	/* Public Methods */
	/******************/

	/** Get the menu represented by the data.
	 *  @param menuItems @array The menu items.
	 *  @return @array The menu element data.
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