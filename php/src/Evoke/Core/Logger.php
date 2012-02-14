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
	protected $DateTime;
	protected $EventManager;
	
	protected $setup;
   
	public function __construct(Array $setup=array())
	{
		$setup += array('DateTime'          => NULL,
		                'Default_Level'     => LOG_INFO,
		                'Default_Level_Str' => 'Level_',
		                'EventManager'      => NULL,
		                'Mask'              => NULL,
		                'Levels'            => array(
			                LOG_EMERG   => 'Emergency',
			                LOG_ALERT   => 'Alert',
			                LOG_CRIT    => 'Critical',
			                LOG_ERR     => 'Error',
			                LOG_WARNING => 'Warning',
			                LOG_NOTICE  => 'Notice',
			                LOG_INFO    => 'Info',
			                LOG_DEBUG   => 'Debug'),
		                'Logging_Mandatory' => true,
		                'Num_Levels'        => 8,
		                'Time_Format'       => 'Y-M-d@H:i:sP');

		if (!$setup['DateTime'] instanceof \DateTime)
		{
			throw new \InvalidArgumentException(__METHOD__ . ' requires DateTime');
		}
		
		if (!$setup['EventManager'] instanceof EventManager)
		{
			throw new \InvalidArgumentException(
				__METHOD__ . ' requires EventManager');
		}

		$this->DateTime = $setup['DateTime'];
		$this->EventManager = $setup['EventManager'];
		
		if (!isset($setup['Mask']))
		{
			// Default to all levels logged.
			$setup['Mask'] = str_repeat('1', $setup['Num_Levels']);
		}

		$this->setup = $setup;
		
		// We observe Log events and remain decoupled to the code that calls us.
		$this->EventManager->connect('Log', array($this, 'log'));

		// Create the Log.Write event as critical when logging is mandatory to
		// ensure that messages are written.
		$this->EventManager->create('Log.Write', $this->setup['Logging_Mandatory']);
	}
   
	/******************/
	/* Public Methods */
	/******************/

	/** Log the message with all of our connected listeners.
	 *  @param message \array Message array containing information to log of the
	 *  form:
	 \verbatim
	 array('Level'   => LOG_LEVEL,          // Number
	 'Message' => 'Message Contents', // String
	 'Method'  => __METHOD__);        // String
	 \endverbatim
       
	 This should then be enhanced by adding the date/time and Level String.
	*/
	public function log(Array $message)
	{
		$message += array('Level' => $this->setup['Default_Level']);

		if (!$this->loggable($message['Level']))
		{
			return;
		}

		$this->DateTime->setTimestamp(time());

		$message += array(
			'Date_Time'    => $this->DateTime->format($this->setup['Time_Format']),
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
		return ($this->setup['Mask'][$level] == true);
	}
   
	/** Set the logging level to the value.
	 *  @param value \bool On or Off.
	 */
	public function setLevel($level, $value=true)
	{
		$this->setup['Mask'][$level] = ($value == true);
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
		if (isset($this->setup['Levels'][$level]))
		{
			return $this->setup['Levels'][$level];
		}

		return $this->setup['Default_Level_Str'] . var_export($level, true);
	}
}
// EOF