<?php
/**
 * CurrentLogin Control View
 *
 * @package View\Control
 */ 
namespace Evoke\View\Control;

use Evoke\View\Data;

/**
 * CurrentLogin Control View
 *
 * @author    Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license   MIT
 * @package   View
 */
class CurrentLogin extends Data
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
		$currentLoginElements = array();
		
		if (isset($this->data['Logged_In'], $this->data['Username']) &&
		    $this->data['Logged_In'])
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
