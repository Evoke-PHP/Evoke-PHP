<?php
/**
 * Login View Control
 *
 * @package View\Control
 */
namespace Evoke\View\Control;

use Evoke\View\View;

/**
 * Login View Control
 *
 * @author    Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license   MIT
 * @package   View\Control
 */
class Login extends View
{
	/******************/
	/* Public Methods */
	/******************/

	/**
	 * Get the view (of the data) to be written.
	 *
	 * @return mixed[] The view data.
	 */
	public function get()
	{
		return array('form',
		             array('action' => '',
		                   'class'  => 'Login',
		                   'method' => 'POST'),
		             array(
			             array('div',
			                   array('class' => 'Username'),
			                   array(array('label',
			                               array('for' => 'Username'),
			                               $this->data['Username']),
			                         array('input',
			                               array('length' => '100',
			                                     'name'   => 'Username',
			                                     'type'   => 'text')))),
			             array('div',
			                   array('class' => 'Password'),
			                   array(array('label',
			                               array('for' => 'Password'),
			                               $this->data['Password']),
			                         array('input',
			                               array('length' => '60',
			                                     'name'   => 'Password',
			                                     'type'   => 'password')))),
			             array('input',
			                   array('name'  => 'Login',
			                         'type'  => 'submit',
			                         'value' =>
			                         $this->data['Login']))));
	}
}
// EOF
