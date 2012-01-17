<?php
namespace Evoke;

class Element_Submit extends Element
{ 
   public function __construct(Array $attribs)
   {
      parent::__construct();
      parent::set(array('input',
			array_merge(array('type' => 'submit'), $attribs)));
   }
}
// EOF