<?php
namespace Evoke\Service\Log;

use DateTime,
	Evoke\Persistance\FilesystemIface,
	InvalidArgumentException;

/**
 * File Logger
 *
 * @author Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license MIT
 * @package Service
 */
class File implements LoggerIface
{
	/**
	 * Whether the file should be appended to.
	 * @var bool	 
	 */
	protected $append;

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
	 * File pointer to the log file.
	 * @var mixed
	 */
	private $filePointer;

	/**
	 * Filesystem
	 * @var Evoke\Persistance\FilesystemIface
	 */
	protected $filesystem;

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
	 * Construct a File Logger object.
	 *
	 * @param Evoke\Persistance\FilesystemIface
	 *                   Filesystem object
	 * @param bool       Whether to append to the file.
	 * @param int(octal) The directory mode for the log file.
	 * @param string     The filename for the log.
	 * @param int(octal) Permissions to set the file to
	 * @param bool       Whether to lock the file for writing.
	 */
	public function __construct(FilesystemIface   $filesystem,
	                            /* Bool */        $append=true,
	                            /* Int (octal) */ $dirMode=0700,
	                            /* String */      $filename='php.log',
	                            /* Int (octal) */ $fileMode=0640,
	                            /* Bool */        $locking=true)
	{
		if (!is_bool($append))
		{
			throw new InvalidArgumentException(
				__METHOD__ . ' requires append as bool');
		}

		if (!is_string($filename))
		{
			throw new InvalidArgumentException(
				__METHOD__ . ' requires filename as string');
		}

		if (!is_bool($locking))
		{
			throw new InvalidArgumentException(__METHOD__ . ' requires locking as bool');
		}

		$this->append     = $append;
		$this->dirMode    = $dirMode;
		$this->filename   = $filename;
		$this->filesystem = $filesystem;
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
		$entry = $date->format('Y-M-d@H:i:sP') . ' [' . $level . '] ' . $message . "\n";

		// Write to the file, with or without file locking.
		if ($this->locking)
		{
			$this->filesystem->flock($this->filePointer, LOCK_EX);
			$this->filesystem->fwrite($this->filePointer, $entry);
			$this->filesystem->flock($this->filePointer, LOCK_UN);
		}
		else
		{
			$this->filesystem->fwrite($this->filePointer, $entry);
		}
	}
   
	/*******************/
	/* Private Methods */
	/*******************/

	/**
	 * Open the log file for output. Creating directories, files as appropriate.
	 * Use the modes from setup for the directory, file and append settings.
	 */
	private function open()
	{
		$writeMode = 'w';
		$dir = dirname($this->filename);

		if (!is_dir($dir))
		{
			$this->filesystem->mkdir(dirname($dir), $this->dirMode, true);
		}
            
		if ($this->append)
		{
			$writeMode = 'a';
		}

		// Open the log file and ensure it is at the right chmod level.
		$this->filePointer =
			$this->filesystem->fopen($this->filename, $writeMode);
		$this->filesystem->chmod($this->filename, $this->fileMode);
		$this->opened = true;
	}
}
// EOF 