<?php
namespace Evoke\Service\Handler;

use ErrorException,
	Evoke\Service\Log\LoggingIface,
	OutOfBoundsException;

/**
 * Error Handler
 *
 * @author Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license MIT
 * @package Service
 */
class Error implements HandlerIface
{
	/**
	 * Logging object.
	 * @var Evoke\Service\Log\LoggingIface
	 */
	protected $logging;

	/**
	 * Construct a system error handler.
	 *
	 * @param Evoke\Service\Log\LoggingIface Logging object.
	 */
	public function __construct(LoggingIface $logging)
	{
		$this->logging = $logging;
	}
   
	/******************/
	/* Public Methods */
	/******************/

	/**
	 * System Error Handler to log error messages.
	 *
	 * @param int    Error Number.
	 * @param string Error String.
	 * @param string File where the error occurred.
	 * @param int    Line where the error occurred.
	 *
	 * @return bool Whether the default system error handler should be
	 *              suppressed.
	 */
	public function handler($errNo, $errStr, $errFile, $errLine)
	{
		// If the error code should not be reported return.
		if (!(error_reporting() & $errNo))
		{
			// Do not allow PHP to report them either as PHP is telling us that
			// they should not be reported.
			return true;
		}
      
		$this->logging->log($errStr . ' in ' . $errFile . ' on ' . $errLine,
		                    $errNo);

		// The easiest way to recover from a recoverable error is by handling an
		// exception.  This ensure the problem is addressed before any related
		// code fails horribly due to unexpected values.
		if ($errNo === E_RECOVERABLE_ERROR)
		{
			throw new ErrorException($errStr, 0, $errNo, $errFile, $errLine);
		}
		
		// Allow PHP to perform its normal reporting of errors.  We have
		// augmented it with our own writing of the error which used our
		// built-in xml writing.
		return false;
	}

	/**
	 * Register the handler.
	 */
	public function register()
	{
		return set_error_handler(array($this, 'handler'));
	}

	/**
	 * Unregister the handler.
	 */
	public function unregister()
	{
		return restore_error_handler();
	}
}
// EOF