<?php
namespace Evoke\Service\Handler;

use ErrorException,
	Evoke\Service\Log\LogIface,
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
	 * Log object.
	 * @var Evoke\Service\Log\LogIface
	 */
	protected $log;

	/**
	 * Construct a system error handler.
	 *
	 * @param Evoke\Service\Log\LogIface Log object.
	 */
	public function __construct(LogIface $log)
	{
		$this->log = $log;
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
      
		$errType = array (E_ERROR             => 'Error',
		                  E_WARNING           => 'Warning',
		                  E_PARSE             => 'Parse',
		                  E_NOTICE            => 'Notice',
		                  E_CORE_ERROR        => 'Core Error',
		                  E_CORE_WARNING      => 'Core Warning',
		                  E_COMPILE_ERROR     => 'Compile Error',
		                  E_COMPILE_WARNING   => 'Compile Warning',
		                  E_USER_ERROR        => 'User Error',
		                  E_USER_WARNING      => 'User Warning',
		                  E_USER_NOTICE       => 'User Notice',
		                  E_STRICT            => 'Strict',
		                  E_DEPRECATED        => 'Deprecated',
		                  E_USER_DEPRECATED   => 'User Deprecated',
		                  E_RECOVERABLE_ERROR => 'Recoverable Error');

		$errTypeStr = isset($errType[$errNo]) ?
			$errType[$errNo] : 'Unknown ' . $errNo;

		$message = 'Error handler [' . $errTypeStr . '] ' . $errStr .
			' in ' . $errFile . ' on ' . $errLine;

		$this->log->log($message, $errNo);

		// The easiest way to recover from a recoverable error is by handling an
		// exception.  This ensure the problem is addressed before any related
		// code fails horribly due to unexpected values.
		if ($errNo === E_RECOVERABLE_ERROR)
		{
			throw new ErrorException($errStr, 0, $errNo, $errFile, $errLine);
		}
		
		// Allow PHP to perform its normal reporting of errors.  We have
		// augmented it with our own writing of the error which used our built-in
		// xml writing.
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