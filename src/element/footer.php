<?php


/// Element_Footer
class Element_Footer extends Element
{ 
   public function __construct()
   {
      parent::__construct(
	 array('div',
	       array('class' => 'Footer'),
	       array('Text'  => 'Website Design by Paul Young')));
   }
}

// EOF