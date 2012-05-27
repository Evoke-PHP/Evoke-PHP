<?php
namespace Evoke\Element\Message;

class Failures extends Array
{ 
	public function __construct(
		/* String */ $elementClass = 'Failure',
		Array        $attribs      = array('class' => 'Failure_Container'),
		Array        $pos          = array())
	{
		parent::__construct($elementClass, $attribs, $pos);
	}
}
// EOF