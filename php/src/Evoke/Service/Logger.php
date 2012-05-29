<?php
namespace Evoke;

/** Logger class to control the logging of messages in the system.
 *  PHP has the following predefined constants defined for us which we use to
 *  log our messages. These default Log Levels are as per syslog entries as
 *  below, listed with their \b Level Name \b Level and \b Meaning.
 *  \verbatim
 LOG_EMERG         0      System is unusable
 LOG_ALERT         1      Immediate action required
 LOG_CRIT          2      Critical conditions
 LOG_ERR           3      Error conditions
 LOG_WARNING       4      Warning conditions
 LOG_NOTICE        5      Normal but significant
 LOG_INFO          6      Informational
 LOG_DEBUG         7      Debug-level messages
 \endverbatim
*/
class Logger
{
	/** @property $dateTime
	 *  DateTIme \object
	 */
	protected $dateTime;

	/** @property $defaultLevel
	 *  The default level to log to \int (defaults to LOG_INFO).
	 */
	protected $defaultLevel;

	/** @property $defaultLevelStr
	 *  The default level string to use.
	 */
	protected $defaultLevelStr;

	/** @property $eventManager
	 *  EventManager \object
	 */
	protected $eventManager;

	/** @property $levels
	 *  \array of levels.
	 */
	protected $levels;

	/** @property $loggingMandatory
	 *  \bool Whether the logging is optional or mandatory.
	 */
	protected $loggingMandatory;

	/** @property $mask
	 *  \int The mask of levels that should be logged.
	 */
	protected $mask;

	/** @property $timeFormat
	 *  The format for the time within the log.
	 */
	protected $timeFormat;
	 
	public function __construct(
		Iface\EventManager $eventManager,
		\DateTime          $dateTime,
		/* Integer */      $defaultLevel     = LOG_INFO,
		/* String */       $defaultLevelStr  = 'Level_',
		Array              $levels           = array(
			LOG_EMERG   => 'Emergency',
			LOG_ALERT   => 'Alert',
			LOG_CRIT    => 'Critical',
			LOG_ERR     => 'Error',
			LOG_WARNING => 'Warning',
			LOG_NOTICE  => 'Notice',
			LOG_INFO    => 'Info',
			LOG_DEBUG   => 'Debug'),
		/* Bool */         $loggingMandatory = true,
		Array              $mask             = array(
			LOG_EMERG   => true,
			LOG_ALERT   => true,
			LOG_CRIT    => true,
			LOG_ERR     => true,
			LOG_WARNING => true,
			LOG_NOTICE  => true,
			LOG_INFO    => true,
			LOG_DEBUG   => true),
		/* String */       $timeFormat       = 'Y-M-d@H:i:sP')
	                            
	{
		$this->dateTime         = $dateTime;
		$this->defaultLevel     = $defaultLevel;
		$this->defaultLevelStr  = $defaultLevelStr;
		$this->eventManager     = $eventManager;
		$this->levels           = $levels;
		$this->loggingMandatory = $loggingMandatory;
		$this->mask             = $mask;
		$this->timeFormat       = $timeFormat;
				
		// We observe Log events and remain decoupled to the code that calls us.
		$this->eventManager->connect('Log', array($this, 'log'));

		// Create the Log.Write event as critical when logging is mandatory to
		// ensure that messages are written.
		$this->eventManager->create('Log.Write', $this->loggingMandatory);
	}
   
	/******************/
	/* Public Methods */
	/******************/

	/** Log the message with all of our connected listeners.
	 *  @param message \array Message array containing information to log of the
	 *  form:
	 *  \verbatim
	 *  array('Level'   => LOG_LEVEL,          // Number
	 *        'Message' => 'Message Contents', // String
	 *        'Method'  => __METHOD__);        // String
	 *  \endverbatim
	 *   
	 * This should then be enhanced by adding the date/time and Level String.
	 */
	public function log(Array $message)
	{
		$message += array('Level' => $this->defaultLevel);

		if (!$this->loggable($message['Level']))
		{
			return;
		}

		$this->dateTime->setTimestamp(time());

		$message += array(
			'Date_Time'    => $this->dateTime->format($this->timeFormat),
			'Level_String' => $this->getLevelString($message['Level']));

		// Notify the listeners of the message!
		$this->eventManager->notify('Log.Write', $message);
	}

	/** Return whether the message has the level to be logged. This is done by
	 *  checking the current log mask against the level.
	 *  @param level \int The level to check.
	 *  \return \bool Whether the level is set in the log mask.
	 */
	public function loggable($level)
	{
		return $this->mask[$level];
	}
   
	/** Set the logging level to the value.
	 *  @param value \bool On or Off.
	 */
	public function setLevel($level, $value=true)
	{
		$this->mask[$level] = $value;
	}
   
	/*********************/
	/* Protected Methods */
	/*********************/

	/** Returns the string of a log level constant or Level_X.
	 *  @param level \int A LOG_* level constant.
	 *  \return \string The string of the log level.
	 */
	public function getLevelString($level)
	{
		if (isset($this->levels[$level]))
		{
			return $this->levels[$level];
		}

		return $this->defaultLevelStr . var_export($level, true);
	}
}
// EOF