<?php
namespace Evoke\View;

class Session extends \Evoke\View\Session
{ 
	/******************/
	/* Public Methods */
	/******************/

	/// Write the session so that we can see it.
	public function write($data)
	{
		$this->writer->write(
			array('div', array('class' => 'Heading'), 'Session'));

		$this->writer->write(
			array('form',
			      array('action' => '',
			            'class'  => 'Clear_Form',
			            'method' => 'POST'),
			      array(array('input',
			                  array('name'  => 'Clear',
			                        'type'  => 'submit',
			                        'value' => 'Clear Session')))));

		$this->writer->write(array('p',
		                           array('class' => 'Session_Data'),
		                           var_export($data, true)));
	}
}
// EOF