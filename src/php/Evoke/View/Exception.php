<?php
/**
 * Exception View
 *
 * @package View
 */
namespace Evoke\View;

use LogicException;

/**
 * Exception View
 *
 * @author Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license MIT
 * @package View
 */
class Exception extends View
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
		// We specify exactly \Exception to avoid a clash with the classname.
		if (!isset($this->data['Exception']) ||
		    !$this->data['Exception'] instanceof \Exception)
		{
			throw new LogicException('needs Data with Exception. Data: ' .
			                         var_export($this->data, true));
		}				

		return array('div',
		             array('class' => 'Exception'),
		             array(array('div',
		                         array('class' => 'Type'),
		                         get_class($this->data['Exception'])),
		                   array('p',
		                         array('class' => 'Message'),
		                         $this->data['Exception']->getMessage()),
		                   array('pre',
		                         array('class' => 'Trace'),
		                         $this->data['Exception']->getTraceAsString())));
	}
}
// EOF
