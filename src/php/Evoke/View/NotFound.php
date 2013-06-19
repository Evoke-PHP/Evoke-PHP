<?php
/**
 * Not Found View
 *
 * @package View
 */
namespace Evoke\View;

/**
 * Not Found View
 *
 * @author    Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license   MIT
 * @package   View
 */
class NotFound extends View
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
		$messageBoxElements = array(
			array('div',
			      array('class' => 'Title'),
			      $this->data['Not_Found_Title']));

		if (isset($this->params['Image_Element']))
		{
			$messageBoxElements[] = $this->params['Image_Element'];
		}

		$messageBoxElements[] =
			array('div',
			      array('class' => 'Description'),
			      $this->data['Not_Found_Description']);
		
		return array('div',
		             array('class' => 'Not_Found Message_Box System'),
		             $messageBoxElements);
	}
}
// EOF
