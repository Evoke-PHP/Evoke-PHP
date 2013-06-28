<?php
/**
 * Not Found View
 *
 * @package View
 */
namespace Evoke\View;

use LogicException;

/**
 * Not Found View
 *
 * @author    Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license   MIT
 * @package   View
 */
class NotFound extends Data
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
		if (!isset($this->data['Description'],
		           $this->data['Title']))
		{
			throw new LogicException('needs data with Description and Title');
		}
		
		return array('div',
		             array('class' => 'Not_Found Message_Box System'),
		             array(array('div',
		                         array('class' => 'Title'),
		                         $this->data['Title']),
		                   array('div',
		                         array('class' => 'Description'),
		                         $this->data['Description'])));
	}
}
// EOF
