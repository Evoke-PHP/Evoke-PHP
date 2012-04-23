<?php
namespace Evoke\Core;
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
	/** @property $DateTime
	 *  DateTIme \object
	 */
	protected $DateTime;

	/** @property $defaultLevel
	 *  The default level to log to \int (defaults to LOG_INFO).
	 */
	protected $defaultLevel;

	/** @property $defaultLevelStr
	 *  The default level string to use.
	 */
	protected $defaultLevelStr;

	/** @property $EventManager
	 *  EventManager \object
	 */
	protected $EventManager;

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
	 
	public function __construct(Array $setup=array())
	{
		$setup += array('DateTime'          => NULL,
		                'Default_Level'     => LOG_INFO,
		                'Default_Level_Str' => 'Level_',
		                'EventManager'      => NULL,
		                'Levels'            => array(LOG_EMERG   => 'Emergency',
		                                             LOG_ALERT   => 'Alert',
		                                             LOG_CRIT    => 'Critical',
		                                             LOG_ERR     => 'Error',
		                                             LOG_WARNING => 'Warning',
		                                             LOG_NOTICE  => 'Notice',
		                                             LOG_INFO    => 'Info',
		                                             LOG_DEBUG   => 'Debug'),
		                'Logging_Mandatory' => true,
		                'Mask'              => NULL,
		                'Time_Format'       => 'Y-M-d@H:i:sP');

		if (!$dateTime instanceof \DateTime)
		{
			throw new \InvalidArgumentException(__METHOD__ . ' needs DateTime');
		}
		
		if (!$eventManager instanceof EventManager)
		{
			throw new \InvalidArgumentException(
				__METHOD__ . ' requires EventManager');
		}
		
		$this->DateTime         = $dateTime;
		$this->defaultLevel     = $defaultLevel;
		$this->defaultLevelStr  = $defaultLevelStr;
		$this->EventManager     = $eventManager;
		$this->levels           = $levels;
		$this->loggingMandatory = $loggingMandatory;
		// Use the mask provided or allow all logging levels.
		$this->mask             = $mask ?:
			str_repeat('1', count($this->levels));
		$this->timeFormat       = $timeFormat;
				
		// We observe Log events and remain decoupled to the code that calls us.
		$this->EventManager->connect('Log', array($this, 'log'));

		// Create the Log.Write event as critical when logging is mandatory to
		// ensure that messages are written.
		$this->EventManager->create('Log.Write', $this->loggingMandatory);
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

		$this->DateTime->setTimestamp(time());

		$message += array(
			'Date_Time'    => $this->DateTime->format($this->timeFormat),
			'Level_String' => $this->getLevelString($message['Level']));
      
		// Notify the listeners of the message!
		$this->EventManager->notify('Log.Write', $message);
	}

	/** Return whether the message has the level to be logged. This is done by
	 *  checking the current log mask against the level.
	 *  @param level \int The level to check.
	 *  \return \bool Whether the level is set in the log mask.
	 */
	public function loggable($level)
	{
		return ($this->mask[$level] == true);
	}
   
	/** Set the logging level to the value.
	 *  @param value \bool On or Off.
	 */
	public function setLevel($level, $value=true)
	{
		$this->mask[$level] = ($value == true);
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