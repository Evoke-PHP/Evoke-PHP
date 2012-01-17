<?php
namespace Evoke;

class Element_Header extends Element
{ 
   public function __construct(Array $setup=array())
   {
      $setup += array('Default_Attribs' => array('class' => 'Header'));
	 
      parent::__construct($setup);
      parent::set(array('div'));
   }
}
// EOF