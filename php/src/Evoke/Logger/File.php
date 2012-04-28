<?php
namespace Evoke\Logger;

use Evoke\Iface;

class File
{
	/** @property $append
	 *  Whether the file should be appended to.
	 */
	protected $append;

	/** @property $dirMode
	 *  The mode for any created directories \int (octal).
	 */
	protected $dirMode;

	/** @property $eventManager
	 *  Event Manager \object
	 */
	protected $eventManager;

	/** @property $fileMode
	 *  The mode for the file \int (octal).
	 */
	protected $fileMode;

	/** @property $filename
	 *  The filename \string to log to.
	 */
	protected $filename;
	
	/** @property $filePointer
	 *  File pointer to the log file.
	 */
	private $filePointer;

	/** @property $filesystem
	 *  Filesystem \object
	 */
	protected $filesystem;

	/** @property $locking
	 *  Whether the file is locked when writing to the file.
	 */
	protected $locking;
	
	/** @property $opened
	 *  \bool Indicates whether or not the resource has been opened.
	 */
	protected $opened = false;


	/** Construct a File Logger object.
	 *  @param eventManager \object EventManager object
	 *  @param $filesystem  \object Filesystem object
	 *  @param append       \bool   Whether to append to the file.
	 *  @param dirMode      \object The directory mode for the log file.
	 *  @param filename     \string The filename for the log.
	 *  @param fileMode     \object Permissions to set the file to
	 *  @param locking      \bool   Whether to lock the file for writing.
	 */
	public function __construct(Iface\EventManager $eventManager,
	                            Iface\Filesystem   $filesystem,
	                            /* Bool */         $append=true,
	                            /* Int (octal) */  $dirMode=0700,
	                            /* String */       $filename='php.log',
	                            /* Int (octal) */  $fileMode=0640,
	                            /* Bool */         $locking=true)
	{
		if (!is_bool($append))
		{
			throw new \InvalidArgumentException(
				__METHOD__ . ' requires append as bool');
		}

		if (!is_string($filename))
		{
			throw new \InvalidArgumentException(
				__METHOD__ . ' requires filename as string');
		}

		if (!is_bool($locking))
		{
			throw new \InvalidArgumentException(__METHOD__ . ' requires locking as bool');
		}

		$this->eventManager = $eventManager;
		$this->filesystem   = $filesystem;
		$this->append       = $append;
		$this->dirMode      = $dirMode;
		$this->filename     = $filename;
		$this->fileMode     = $fileMode;
		$this->locking      = $locking;
	}
   
	/******************/
	/* Public Methods */
	/******************/

	/** Logs a message to the file.
	 *  @param message \array The message to log as created by \ref Logger::log.
	 */
	public function write(Array $message)
	{
		if (!$this->opened)
		{
			$this->open();
		}
      
		// Ensure the message is a string.
		$entry = $message['Date_Time'] . ' ' . $message['Method'] . ' [' .
			$message['Level_String'] . '] ' . $message['Message'] . "\n";

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

	/** Open the log file for output. Creating directories, files as appropriate.
	 *  Use the modes from setup for the directory, file and append settings.
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