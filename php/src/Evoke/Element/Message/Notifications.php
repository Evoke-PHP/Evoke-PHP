<?php
namespace Evoke\Element\Message;

class Notifications extends Array
{ 
	public function __construct(
		/* String */ $elementClass = 'Notification',
		Array        $attribs      = array('class' => 'Notification_Container'),
		Array        $pos          = array());
	{
		parent::__construct($elementClass, $attribs, $pos);
	}
}
// EOF