<?php
/**
 * Error View
 *
 * @package View\XHTML
 */
namespace Evoke\View\XHTML;

use Evoke\View\ViewIface,
	LogicException;

/**
 * Error View
 *
 * @author    Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license   MIT
 * @package   View\XHTML
 */
class Error implements ViewIface
{
	/**
	 * Protected Properties.
	 *
	 * @var string[] $error   Error to get the view of.
	 * @var string   $unknown String for an unknown part of the error.
	 */	 
	protected $error, $unknown;

	/**
	 * Construct an Error view.
	 *
	 * @param string  String for an unkown part of the error.
	 */
	public function __construct(/* String */ $unknown = '<Unknown>')
	{
		$this->unknown = $unknown;
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
		if (!isset($this->error))
		{
			throw new LogicException('needs error');
		}
		
		$error = array_merge(array('file'    => $this->unknown,
		                           'line'    => $this->unknown,
		                           'message' => $this->unknown,
		                           'type'    => $this->unknown),
		                     $this->error);
			
		return array(
			'div',
			array('class' => 'Error'),
			array(array('div',
			            array('class' => 'Details'),
			            array(array('span',
			                        array('class' => 'Type'),
			                        $this->getTypeString($error['type'])),
			                  array('span',
			                        array('class' => 'File'),
			                        $error['file']),
			                  array('span',
			                        array('class' => 'Line'),
			                        $error['line']))),
			      array('p',
			            array('class' => 'Message'),
			            $error['message'])));
	}

	/**
	 * Set the error for the view.
	 *
	 * @param mixed[] Error.
	 */
	public function set(Array $error)
	{
		$this->error = $error;
	}
	
	/*******************/
	/* Private Methods */
	/*******************/

	/**
	 * Get the type of error message based on the error number.
	 *
	 * @param int The error number.
	 *
	 * @return string The error type as a string.
	 */
	private function getTypeString($number)
	{
		$errorMap = array(E_ERROR         	  => 'E_ERROR',
		                  E_WARNING       	  => 'E_WARNING',
		                  E_PARSE         	  => 'E_PARSE',
		                  E_NOTICE        	  => 'E_NOTICE',
		                  E_CORE_ERROR    	  => 'E_CORE_ERROR',
		                  E_CORE_WARNING      => 'E_CORE_WARNING',
		                  E_CORE_ERROR        => 'E_COMPILE_ERROR',
		                  E_CORE_WARNING      => 'E_COMPILE_WARNING',
		                  E_USER_ERROR        => 'E_USER_ERROR',
		                  E_USER_WARNING      => 'E_USER_WARNING',
		                  E_USER_NOTICE       => 'E_USER_NOTICE',
		                  E_STRICT            => 'E_STRICT',
		                  E_RECOVERABLE_ERROR => 'E_RECOVERABLE_ERROR',
		                  E_DEPRECATED        => 'E_DEPRECATED',
		                  E_USER_DEPRECATED   => 'E_USER_DEPRECATED');

		if (isset($errorMap[$number]))
		{
			return $errorMap[$number];
		}
		                  
		return $this->unknown;
	}
}
// EOF
