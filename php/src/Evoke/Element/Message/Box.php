<?php
namespace Evoke\Element\Message;

class Box extends \Evoke\Element\Base
{ 
	public function __construct(Array $attribs=array('class' => 'Message_Box Info'),
	                            Array $pos=array())
	{
		parent::__construct($attribs, $pos);
	}
   
	/******************/
	/* Public Methods */
	/******************/

	public function set(Array $message)
	{
		$message += array('Description' => NULL,
		                  'Title'       => NULL);

		if (!isset($message['Description']))
		{
			throw new \InvalidArgumentException(__METHOD__ . ' needs Description');
		}

		if (!isset($message['Title']))
		{
			throw new \InvalidArgumentException(__METHOD__ . ' needs Title');
		}
      
		return parent::set(
			array('div',
			      array(),
			      array(array('div',
			                  array('class' => 'Title'),
			                  $message['Title']),
			            array('div',
			                  array('class' => 'Description'),
			                  $message['Description']))));
	}
}
// EOF