<?php
/**
 * Exception View
 *
 * @package View\XHTML
 */
namespace Evoke\View\XHTML;

use Evoke\View\ExceptionIface,
	LogicException;

/**
 * Exception View
 *
 * @author Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license MIT
 * @package View\XHTML
 */
class Exception implements ExceptionIface
{
	/**
	 * The exception that we are viewing.
	 * @var \Exception
	 */
	protected $exception;
	
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
		if (!isset($this->exception))
		{
			throw new LogicException('needs exception to be set.');
		}				

		return array('div',
		             array('class' => 'Exception'),
		             array(array('div',
		                         array('class' => 'Type'),
		                         get_class($this->exception)),
		                   array('p',
		                         array('class' => 'Message'),
		                         $this->exception->getMessage()),
		                   array('pre',
		                         array('class' => 'Trace'),
		                         $this->exception->getTraceAsString())));
	}

	/**
	 * Set the exception for the view.
	 *
	 * @param \Exception The exception for the view.	 
	 */
	public function setException(\Exception $exception)
	{
		$this->exception = $exception;
	}
}
// EOF
