<?php


/// Element_Submit
class Element_Submit extends Element
{ 
   public function __construct(Array $attribs)
   {
      parent::__construct(
	 array('input',
	       array_merge(array('type' => 'submit'), $attribs)));
   }
}

// EOF