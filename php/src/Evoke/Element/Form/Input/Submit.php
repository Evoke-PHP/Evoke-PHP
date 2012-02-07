<?php
namespace Evoke\Element\Input;

class Submit extends \Evoke\Element
{ 
	public function __construct(Array $attribs)
	{
		parent::__construct();
		parent::set(array('input',
		                  array_merge(array('type' => 'submit'), $attribs)));
	}
}
// EOF