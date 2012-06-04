<?php
namespace Evoke\Service\Handler;

use ErrorException,
	Evoke\Service\Log\LogIface,
	OutOfBoundsException;

class Error implements HandlerIface
{
	/** @property $log
	 *  @object Log
	 */
	protected $log;

	public function __construct(LogIface $log)
	{
		$this->log = $log;
	}
   
	/******************/
	/* Public Methods */
	/******************/

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

	public function register()
	{
		return set_error_handler(array($this, 'handler'));
	}

	public function unregister()
	{
		return restore_error_handler();
	}
}
// EOF