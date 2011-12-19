<?php


/// Element_Failures
class Element_Failures extends Element_Message_Array
{ 
   public function __construct($failures, $setup=array())
   {
      $setup = array_merge(
	 array('Container_Attribs' => array('class' => 'Failure_Container'),
	       'Element_Class' => 'Failure'),
	 $setup);

      parent::__construct($failures, $setup);
   }
}

// EOF