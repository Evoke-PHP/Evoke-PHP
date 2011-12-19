<?php


class Element_Header extends Element
{ 
   public function __construct($setup=array())
   {
      parent::__construct(array('div', array('class' => 'Header')));
   }
}

// EOF