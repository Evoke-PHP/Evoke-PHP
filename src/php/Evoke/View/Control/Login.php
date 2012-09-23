<?php
/**
 * Login View
 *
 * @package View
 */
namespace Evoke\View\Control;

use Evoke\Model\Data\TranslationsIface,
	Evoke\View\ViewIface;

/**
 * Login View
 *
 * @author Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license MIT
 * @package View
 */
class Login implements ViewIface
{
	/**
	 * Translations
	 * @var TranslationsIface
	 */
	protected $translations;

	/**
	 * Construct a Login object.
	 *
	 * @param TranslationsIface Translations.
	 */
	public function __construct(TranslationsIface $translations)
	{
		$this->translations = $translations;
	}

	/******************/
	/* Public Methods */
	/******************/

	/**
	 * Get the view (of the data) to be written.
	 *
	 * @param mixed[] Parameters for retrieving the view.
	 *
	 * @return mixed[] The view data.
	 */
	public function get(Array $params = array())
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
			                               $this->translations->tr('Username')),
			                         array('input',
			                               array('length' => '100',
			                                     'name'   => 'Username',
			                                     'type'   => 'text')))),
			             array('div',
			                   array('class' => 'Password'),
			                   array(array('label',
			                               array('for' => 'Password'),
			                               $this->translations->tr('Password')),
			                         array('input',
			                               array('length' => '60',
			                                     'name'   => 'Password',
			                                     'type'   => 'password')))),
			             array('input',
			                   array('name'  => 'Login',
			                         'type'  => 'submit',
			                         'value' =>
			                         $this->translations->tr('Login')))));
	}
}
// EOF
