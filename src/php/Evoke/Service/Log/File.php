<?php
/**
 * File Logger
 *
 * @package Service\Log
 */
namespace Evoke\Service\Log;

use DateTime,
	RuntimeException;

/**
 * File Logger
 *
 * @author    Paul Young <evoke@youngish.org>
 * @copyright Copyright (c) 2014 Paul Young
 * @license   MIT
 * @package   Service\Log
 */
class File implements LoggerIface
{
	/**
	 * The mode for any created directories.
	 * @var int (octal)
	 */
	protected $dirMode;

	/**
	 * The mode for the file.
	 * @var int (octal)
	 */
	protected $fileMode;

	/**
	 * The filename to log to.
	 * @var string
	 */
	protected $filename;
	
	/**
	 * Whether the file is locked when writing to the file.
	 * @var bool
	 */
	protected $locking;
	
	/**
	 * Indicates whether or not the resource has been opened.
	 * @var bool
	 */
	protected $opened = false;

	/**
	 * File pointer to the log file.
	 * @var mixed
	 */
	private $filePointer;

	/**
	 * Construct a File Logger object.
	 *
	 * @param string          The filename for the log.
	 * @param int(octal) 	  The directory mode for the log file.
	 * @param int(octal) 	  Permissions to set the file to
	 * @param bool       	  Whether to lock the file for writing.
	 */
	public function __construct(/* String */      $filename,
	                            /* Int (octal) */ $dirMode  = 0700,
	                            /* Int (octal) */ $fileMode = 0640,
	                            /* Bool */        $locking  = true)
	{
		$this->dirMode    = $dirMode;
		$this->filename   = $filename;
		$this->fileMode   = $fileMode;
		$this->locking    = $locking;
	}
   
	/******************/
	/* Public Methods */
	/******************/

	/**
	 * Logs a message to the file.
	 *
	 * @param DateTime The DateTime for the log message.
	 * @param mixed    The message to log.
	 * @param mixed    The level of the message.
	 */
	public function log(DateTime $date, $message, $level)
	{
		if (!$this->opened)
		{
			$this->open();
		}
      
		// Ensure the message is a string.
		$entry = $date->format('Y-M-d@H:i:sP') . ' [' . $level . '] ' .
			$message . "\n";

		// Write to the file, with or without file locking.
		if ($this->locking)
		{
			flock($this->filePointer, LOCK_EX);
			fwrite($this->filePointer, $entry);
			flock($this->filePointer, LOCK_UN);
		}
		else
		{
			fwrite($this->filePointer, $entry);
		}
	}
   
	/*******************/
	/* Private Methods */
	/*******************/

	/**
	 * Open the log file for output. Creating directories and the log file if
	 * required using the correct mode.
	 */
	private function open()
	{
		$dir = dirname($this->filename);

		if (!is_dir($dir) &&
		    !mkdir(dirname($dir), $this->dirMode, true))
		{
			throw new RuntimeException('Cannot make log directory.');
		}
		
		if (!chmod($dir, $this->dirMode))
		{
			throw new RuntimeException('Cannot chmod log directory.');
		}

		if (!touch($this->filename))
		{
			throw new RuntimeException('Cannot touch log file.');
		}

		if (!chmod($this->filename, $this->fileMode))
		{
			throw new RuntimeException('Cannot chmod log file.');
		}
		
		// Open the log file for appending.
		$this->filePointer = fopen($this->filename, 'a');

		if ($this->filePointer == false)
		{
			throw new RuntimeException('Cannot open log file.');
		}

		$this->opened = true;
	}
}
// EOF 