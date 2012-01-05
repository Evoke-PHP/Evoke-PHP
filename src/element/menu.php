<?php
/// Element_Menu
class Element_Menu extends Element
{
   private $lang;
   protected $setup;

   public function __construct($setup=array())
   {
      $this->setup = array_merge(
	 array('Data'         => array(),
	       'Menu_Attribs' => array(),
	       'Translator'   => NULL),
	 $setup);

      if (!$this->setup['Translator'] instanceof Translator)
      {
	 throw new InvalidArgumentException(
	    __METHOD__ . ' needs Translator');
      }

      $this->lang = $this->setup['Translator']->getLanguage();
      
      parent::__construct(
	 array('ul',
	       $this->setup['Menu_Attribs'],
	       array('Children' => $this->buildMenu($this->setup['Data']))));
   }

   /******************/
   /* Public Methods */
   /******************/

   private function buildMenu($data, $level = 0)
   {
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
			   array('Text' => $menuItem['Text_' . $this->lang])),
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
			   array('Text' => $menuItem['Text_' . $this->lang]))
		     )));
	 }
      }
      
      return $menu;
   }
}
// EOF