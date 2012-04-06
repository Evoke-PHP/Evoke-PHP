<?php
namespace Evoke\View;

class Session extends Base
{ 
	/******************/
	/* Public Methods */
	/******************/

	/// Write the session so that we can see it.
	public function write($data)
	{
		$this->Writer->write(
			array('div',
			      array('class' => 'Heading'),
			      array('Text' => 'Session')));

		$this->Writer->write(
			array('form',
			      array('action' => '',
			            'class'  => 'Clear_Form',
			            'method' => 'POST'),
			      array('Children' => array(
				            array('input',
				                  array('name'  => 'Clear',
				                        'type'  => 'submit',
				                        'value' => 'Clear Session'))))));
		$this->Writer->write(array('div'));
		$this->Writer->write(
			array('p',
			      array('class' => 'Session_Data'),
			      array('Text' => var_export($data, true))));
	}
}
// EOF