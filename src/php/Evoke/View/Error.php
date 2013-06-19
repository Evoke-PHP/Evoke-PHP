<?php
/**
 * Error View
 *
 * @package View
 */
namespace Evoke\View;

/**
 * Error View
 *
 * @author    Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license   MIT
 * @package   View
 */
class Error implements ViewIface
{
	protected 
		/**
		 * ErrorParams
		 * @var string[]
		 */
		$errorParams,

		/**
		 * Is Detailed
		 * @var bool
		 */
		$isDetailed,
	
		/**
		 * Unknown String
		 * @var string
		 */
		$unknownString;

	/**
	 * Construct a Error object.
	 *
	 * @param string[] ErrorParams.
	 * @param string   UnknownString.
	 */
	public function __construct(Array        $errorParams,
	                            /* Bool   */ $isDetailed,
	                            /* String */ $unknownString = '<Unknown>')
	{
		$this->errorParams   = $errorParams;
		$this->isDetailed    = $isDetailed;
		$this->unknownString = $unknownString;
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
		/// \todo Use data instead of params.
		
		$descriptionElements = array(
			array('div',
			      array('class' => 'General'),
			      'An error has occurred. We have been notified. ' .
			      'We will fix this as soon as possible.'));

		if ($this->isDetailed)
		{
			$params = array_merge(array('file'    => $unknownString,
			                            'line'    => $unknownString,
			                            'message' => $unknownString,
			                            'type'    => $unknownString),
			                      $this->errorParams,
			                      $params);
			
			$descriptionElements[] =
				array('div',
				      array('class' => 'Error'),
				      array(array('div',
				                  array('class' => 'Details'),
				                  array(array('span',
				                              array('class' => 'Type'),
				                              $this->getTypeString(
					                              $params['type'])),
				                        array('span',
				                              array('class' => 'File'),
				                              $params['file']),
				                        array('span',
				                              array('class' => 'Line'),
				                              $params['line']))),
				            array('p',
				                  array('class' => 'Message'),
				                  $params['message'])));
		}

		return array(
			'div',
			array('class' => 'Message_Box System'),
			array(array('div',
			            array('class' => 'Title'),
			            'Error'),
			      array('div',
			            array('class' => 'Description'),
			            $descriptionElements)));
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
		                  
		return 'UNKNOWN';
	}
}
// EOF
