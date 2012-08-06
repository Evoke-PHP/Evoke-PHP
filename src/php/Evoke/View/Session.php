<?php
namespace Evoke\View\XHTML;

use Evoke\View\ViewIface;

/**
 * Session
 *
 * @author Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license MIT
 * @package View
 */
class Session implements ViewIface
{
	/******************/
	/* Public Methods */
	/******************/

	/**
	 * Get the view of the session.
	 *
	 * @param mixed[] Parameters to the view.
	 */
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