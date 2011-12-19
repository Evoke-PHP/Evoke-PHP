<?php


/// Element_Notifications
class Element_Notifications extends Element_Message_Array
{ 
   public function __construct($notifications, $setup=array())
   {
      $setup = array_merge(
	 array('Container_Attribs' => array('class' => 'Notification_Container'),
	       'Element_Class' => 'Notification'),
	 $setup);

      parent::__construct($notifications, $setup);
   }
}

// EOF