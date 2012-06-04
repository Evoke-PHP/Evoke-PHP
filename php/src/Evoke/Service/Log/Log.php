<?php
namespace Evoke\Service\Log;

use DateTime;

class Log implements LogIface
{ 
	/** @property $dateTime
	 *  @object DateTime for each log message.
	 */
	protected $dateTime;

	/** @property $observers
	 *  @array The Logging objects that are observing the log messages.
	 */
	protected $observers = array();
	
	public function __construct(DateTime $dateTime)
	{
		$this->dateTime = $dateTime;
	}

	/******************/
	/* Public Methods */
	/******************/

	/** Add a logger to the observer list.
	 *  @param observer @object The logger to add.
	 */
	public function attach(LoggerIface $observer)
	{
		$this->observers[] = $observer;
	}

	/** Remove a logger from the observer list.
	 *  @param observer @object The logger to remove (If there are more than one
	 *                          occurences of the logger in the list then only
	 *                          one is removed).
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

	/** Log a message by calling all of the observers in the observer list.
	 *  @param message @mixed The message to log.
	 *  @param level   @mixed The level of the message to log.
	 */
	public function log($message, $level)
	{
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
