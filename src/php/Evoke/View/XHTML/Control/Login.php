<?php
/**
 * Login View Control
 *
 * @package View\XHTML\Control
 */
namespace Evoke\View\XHTML\Control;

use Evoke\View\Data,
	LogicException;

/**
 * Login View Control
 *
 * @author    Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license   MIT
 * @package   View\XHTML\Control
 */
class Login extends Data
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
		if (!isset($this->data['Login'],
		           $this->data['Password'],
		           $this->data['Username']))
		{
			throw new LogicException(
				'needs data with Login, Password and Username');
		}

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
