<?php
/**
 * CurrentLogin View
 *
 * @package View
 */ 
namespace Evoke\View\Control;

use Evoke\Model\Data\TranslationsIface,
	Evoke\View\View;

/**
 * CurrentLogin View
 *
 * @author Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license MIT
 * @package View
 */
class CurrentLogin extends View
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
	 * @return mixed[] The view data.
	 */
	public function get()
	{
		$currentLoginElements = array();
		
		if (isset($this->data['Logged_In']) && $this->data['Logged_In'])
		{
			$currentLoginElements = array(
				array('span',
				      array(),
				      $this->data['Username']),
				array('form',
				      array('action' => '',
				            'method' => 'POST'),
				      array(array('input',
				                  array('type' => 'submit',
				                        'name' => 'Logout',
				                        'value' => 'Logout')))));
		}
		
		return array('div',
		             array('class' => 'Current_Login'),
		             $currentLoginElements);
	}
}
// EOF
