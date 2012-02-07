<?php
namespace Evoke\Element\Message;

class Notifications extends Array
{ 
	public function __construct(Array $setup=array())
	{
		$setup += array('Container_Attribs' => array('class' => 'Notification_Container'),
		                'Element_Class'     => 'Notification');
		parent::__construct($setup);
	}
}
// EOF