<?php
namespace Evoke\Element\Message;

class Failures extends Array
{ 
	public function __construct(
		Array $attribs=array('class' => 'Failure_Container'),
		Array $pos=array())
	{
		
		$setup += array(
			'Container_Attribs' => array('class' => 'Failure_Container'),
			'Element_Class'     => 'Failure');

		parent::__construct($setup);
	}
}
// EOF