<?php
namespace Evoke\View\XHTML;

use Evoke\View\ViewIface;

class Session implements ViewIface
{
	/******************/
	/* Public Methods */
	/******************/

	/// Write the session so that we can see it.
	public function get(Array $params = array())
	{
		return array(
			'div',
			array('class' => 'Session'),
			array(array('div', array('class' => 'Heading'), 'Session'),
			      array('form',
			            array('action' => '',
			                  'class'  => 'Clear_Form',
			                  'method' => 'POST'),
			            array(array('input',
			                        array('name'  => 'Clear',
			                              'type'  => 'submit',
			                              'value' => 'Clear Session')))),
			      array('p',
			            array('class' => 'Session_Data'),
			            var_export($params, true))));
	}
}
// EOF