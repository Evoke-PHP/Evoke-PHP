<?php
class Element_Failures extends Element_Message_Array
{ 
   public function __construct(Array $setup=array())
   {
      $setup += array(
	 'Container_Attribs' => array('class' => 'Failure_Container'),
	 'Element_Class'     => 'Failure');

      parent::__construct($setup);
   }
}
// EOF