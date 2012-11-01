<?php
/**
 * Error
 *
 * @package View
 */
namespace Evoke\View;

/**
 * Error
 *
 * @author Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license MIT
 * @package View
 */
class Error implements ViewIface
{
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
		$descriptionElements = array(
			array('div',
			      array('class' => 'General'),
			      'An error has occurred. We have been notified. ' .
			      'We will fix this as soon as possible.'));
			
		foreach ($params as $detailType => $detailValue)
		{
			$descriptionElements[] =
				array('span',
				      array('class' => ucfirst($detailType)),
				      $detailValue);
		}
		
		return array(
			'div',
			array('class' => 'Not_Found Message_Box System'),
			array(array('div',
			            array('class' => 'Title'),
			            'Error'),
			      array('div',
			            array('class' => 'Description'),
			            $descriptionElements)));
	}
}
// EOF
