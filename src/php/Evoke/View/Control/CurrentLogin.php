<?php
/**
 * CurrentLogin View
 *
 * @package View
 */ 
namespace Evoke\View\Control;

use Evoke\Model\Data\TranslationsIface,
	Evoke\View\ViewIface;

/**
 * CurrentLogin View
 *
 * @author Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license MIT
 * @package View
 */
class CurrentLogin implements ViewIface
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
		$currentLoginElements = empty($params['Username']) ?
			array(array('span',
			            array(),
			            $this->translations->tr('Not_Logged_In')),
			      array('a',
			            array('href' => $params['Login_Page']))) :
			array(array('span',
			            array(),
			            $params['Username']),
			      array('form',
			            array('action' => '',
			                  'method' => 'POST'),
			            array(array('input',
			                        array('type' => 'submit',
			                              'name' => 'Logout',
			                              'value' => 'Logout')))));
		
		return array('div',
		             array('class' => 'Current_Login'),
		             $currentLoginElements);

	}
}
// EOF
