<?php
namespace Evoke;

class Element_Notifications extends Element_Message_Array
{ 
   public function __construct(Array $setup=array())
   {
      $setup += array('Container_Attribs' => array('class' => 'Notification_Container'),
		      'Element_Class'     => 'Notification');
      parent::__construct($setup);
   }
}
// EOF