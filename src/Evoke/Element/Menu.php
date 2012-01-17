<?php
namespace Evoke;

class Element_Menu extends Element
{
   protected $setup;

   public function __construct($setup=array())
   {
      $setup += array('Menu_Attribs' => array(),
		      'Translator'   => NULL);

      parent::__construct($setup);

      if (!$this->setup['Translator'] instanceof Translator)
      {
	 throw new \InvalidArgumentException(
	    __METHOD__ . ' needs Translator');
      }
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
	       $this->setup['Menu_Attribs'],
	       array('Children' => $this->buildMenu($menuItems))));
   }
   
   /*******************/
   /* Private Methods */
   /*******************/

   private function buildMenu($data, $level = 0)
   {
      $lang = $this->setup['Translator']->getLanguage();
      $menu = array();
      
      foreach ($data as $menuItem)
      {
	 if (isset($menuItem['Children']))
	 {
	    $menu[] = array(
	       'li',
	       array('class' => 'Level_' . $level),
	       array(
		  'Children' => array(
		     array('a',
			   array('href' => $menuItem['Href']),
			   array('Text' => $menuItem['Text_' . $lang])),
		     array('ul',
			   array(),
			   array('Children' => $this->buildMenu(
				    $menuItem['Children'], ++$level))))));
	 }
	 else
	 {
	    $menu[] = array(
	       'li',
	       array('class' => 'Level_' . $level),
	       array(
		  'Children' => array(
		     array('a',
			   array('href' => $menuItem['Href']),
			   array('Text' => $menuItem['Text_' . $lang])))));
	 }
      }
      
      return $menu;
   }
}
// EOF