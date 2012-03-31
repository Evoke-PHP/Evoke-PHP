<?php
namespace Evoke\Core\Logger;

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

	/** @property $EventManager
	 *  Event Manager \object
	 */
	protected $EventManager;

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

	/** @property $Filesystem
	 *  Filesystem \object
	 */
	protected $Filesystem;

	/** @property $locking
	 *  Whether the file is locked when writing to the file.
	 */
	protected $locking;
	
	/** @property $opened
	 *  \bool Indicates whether or not the resource has been opened.
	 */
	protected $opened = false;

	/** Constructs a new Logger_File object.
	 *  @param conf  \array   The configuration array.
	 */
	public function __construct(Array $setup=array())
	{
		$setup += array('Append'       => true,
		                'Dir_Mode'     => 0700,
		                'EventManager' => NULL,
		                'Filename'     => 'php.log',
		                'File_Mode'    => 0640,
		                'Filesystem'   => NULL,
		                'Locking'      => true);

		if (!$setup['EventManager'] instanceof \Evoke\Core\Iface\EventManager)
		{
			throw new \InvalidArgumentException(
				__METHOD__ . ' requires EventManager');
		}
		
		if (!$setup['Filesystem'] instanceof \Evoke\Core\Filesystem)
		{
			throw new \InvalidArgumentException(__METHOD__ . ' needs Filesystem');
		}

		$this->append       = $setup['Append'];
		$this->dirMode      = $setup['Dir_Mode'];
		$this->filename     = $setup['Filename'];
		$this->fileMode     = $setup['File_Mode'];
		$this->EventManager = $setup['EventManager'];
		$this->Filesystem   = $setup['Filesystem'];
		$this->locking      = $setup['Locking'];

		$this->EventManager->connect('Log.Write', array($this, 'write'));
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
			$this->Filesystem->flock($this->filePointer, LOCK_EX);
			$this->Filesystem->fwrite($this->filePointer, $entry);
			$this->Filesystem->flock($this->filePointer, LOCK_UN);
		}
		else
		{
			$this->Filesystem->fwrite($this->filePointer, $entry);
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
			$this->Filesystem->mkdir(dirname($dir), $this->dirMode, true);
		}
            
		if ($this->append)
		{
			$writeMode = 'a';
		}

		// Open the log file and ensure it is at the right chmod level.
		$this->filePointer =
			$this->Filesystem->fopen($this->filename, $writeMode);
		$this->Filesystem->chmod($this->filename, $this->fileMode);
		$this->opened = true;
	}
}
// EOF