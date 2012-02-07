<?php
namespace Evoke\Core\Logger;

class File
{
	/// Indicates whether or not the resource has been opened.
	protected $opened = false;

	/// File pointer to the log file.
	private $fp = false;

	/** Constructs a new Logger_File object.
	 *  @param conf  \array   The configuration array.
	 */
	public function __construct(Array $setup=array())
	{
		$this->setup = array_merge(
			array('Append'       => true,
			      'Dir_Mode'     => 0700,
			      'EventManager' => NULL,
			      'Filesystem'   => NULL,
			      'File_Mode'    => 0640,
			      'Filename'     => 'php.log',
			      'Locking'      => true),
			$setup);

		if (!$this->setup['EventManager'] instanceof \Evoke\Core\EventManager)
		{
			throw new \InvalidArgumentException(
				__METHOD__ . ' needs EventManager');
		}

		if (!$this->setup['Filesystem'] instanceof \Evoke\Core\Filesystem)
		{
			throw new \InvalidArgumentException(
				__METHOD__ . ' needs Filesystem');
		}

		$this->setup['EventManager']->connect(
			'Log.Write', array($this, 'write'));
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
		if ($this->setup['Locking'])
		{
			$this->setup['Filesystem']->flock($this->fp, LOCK_EX);
			$this->setup['Filesystem']->fwrite($this->fp, $entry);
			$this->setup['Filesystem']->flock($this->fp, LOCK_UN);
		}
		else
		{
			$this->setup['Filesystem']->fwrite($this->fp, $entry);
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
		$dir = dirname($this->setup['Filename']);

		if (!is_dir($dir))
		{
			$this->setup['Filesystem']->mkdir(
				dirname($dir), $this->setup['Dir_Mode'], true);
		}
            
		if ($this->setup['Append'])
		{
			$writeMode = 'a';
		}

		// Open the log file and ensure it is at the right chmod level.
		$this->fp = $this->setup['Filesystem']->fopen(
			$this->setup['Filename'], $writeMode);
      
		$this->setup['Filesystem']->chmod(
			$this->setup['Filename'], $this->setup['File_Mode']);

		$this->opened = true;
	}
}
// EOF