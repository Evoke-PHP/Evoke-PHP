<?php
namespace Evoke\Service\Log;

use DateTime;

/**
 * Log
 *
 * @author Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license MIT
 * @package Service
 */
class Log implements LogIface
{ 
	/**
	 * DateTime for each log message.
	 * @var DateTime
	 */
	protected $dateTime;

	/**
	 * The Logging objects that are observing the log messages.
	 * @var Evoke\Service\Log\LoggerIface
	 */
	protected $observers = array();

	/**
	 * Construct a Log object.
	 *
	 * @param DateTime DateTime object.
	 */
	public function __construct(DateTime $dateTime)
	{
		$this->dateTime = $dateTime;
	}

	/******************/
	/* Public Methods */
	/******************/

	/**
	 * Add a logger to the observer list.
	 *
	 * @param Evoke\Service\Log\LoggerIface The logger to add.
	 */
	public function attach(LoggerIface $observer)
	{
		$this->observers[] = $observer;
	}

	/**
	 * Remove a logger from the observer list.
	 *
	 * @param Evoke\Service\Log\LoggerIface
	 *           The logger to remove (If there are more than one occurences of
	 *           the logger in the list then only one is removed).
	 */
	public function detach(LoggerIface $observer)
	{
		foreach ($this->observers as $key => $attachedObserver)
		{
			if ($attachedObserver === $observer)
			{
				unset($this->observers[$key]);
				return;
			}
		}
	}

	/**
	 * Log a message by calling all of the observers in the observer list.
	 *
	 * @param mixed The message to log.
	 * @param mixed The level of the message to log.
	 */
	public function log($message, $level)
	{
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
		                  E_RECOVERABLE_ERROR => 'Recoverable Error',
		                  E_DEPRECATED        => 'Deprecated',
		                  E_USER_DEPRECATED   => 'User Deprecated');

		if (isset($errType[$level]))
		{
			$level = $errType[$level];
		}
		
		$this->dateTime->setTimestamp(time());

		if (empty($this->observers))
		{
			error_log(__METHOD__ . ' no log observers!  Message: ' . $message .
			          ' level: ' . $level);
		}
		
		foreach ($this->observers as $observer)
		{
			$observer->log($this->dateTime, $message, $level);
		}
	}
}

// EOF