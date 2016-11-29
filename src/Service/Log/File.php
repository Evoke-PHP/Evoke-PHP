<?php
declare(strict_types = 1);
/**
 * File Logger
 *
 * @package Service\Log
 */
namespace Evoke\Service\Log;

use DateTime;
use RuntimeException;

/**
 * File Logger
 *
 * @author    Paul Young <evoke@youngish.org>
 * @copyright Copyright (c) 2015 Paul Young
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
     * @param string $filename The filename for the log.
     * @param int    $dirMode  The directory mode for the log file (octal).
     * @param int    $fileMode The file permissions for the log file (octal).
     * @param bool   $locking  Whether to lock the file for writing.
     */
    public function __construct(string $filename, int $dirMode = 0700, int $fileMode = 0640, bool $locking = true)
    {
        $this->dirMode  = $dirMode;
        $this->filename = $filename;
        $this->fileMode = $fileMode;
        $this->locking  = $locking;
    }

    /******************/
    /* Public Methods */
    /******************/

    /**
     * Logs a message to the file.
     *
     * @param DateTime $date    The DateTime for the log message.
     * @param mixed    $message The message to log.
     * @param int      $level   The level of message.
     */
    public function log(DateTime $date, $message, $level)
    {
        if (!$this->opened) {
            $this->open();
        }

        // Ensure the message is a string.
        $entry = $date->format('Y-M-d@H:i:sP') . ' [' . $level . '] ' . $message . "\n";

        // Write to the file, with or without file locking.
        if ($this->locking) {
            flock($this->filePointer, LOCK_EX);
            fwrite($this->filePointer, $entry);
            flock($this->filePointer, LOCK_UN);
        } else {
            fwrite($this->filePointer, $entry);
        }
    }

    /*******************/
    /* Private Methods */
    /*******************/

    /**
     * Open the log file for output. Creating directories and the log file if required using the correct mode.
     *
     * @throws RuntimeException If the file cannot be opened in the system.
     */
    private function open()
    {
        $dir = dirname($this->filename);

        if (!is_dir($dir) &&
            !mkdir(dirname($dir), $this->dirMode, true)
        ) {
            throw new RuntimeException('Cannot make log directory.');
        }

        if (!chmod($dir, $this->dirMode)) {
            throw new RuntimeException('Cannot chmod log directory.');
        }

        if (!touch($this->filename)) {
            throw new RuntimeException('Cannot touch log file.');
        }

        if (!chmod($this->filename, $this->fileMode)) {
            throw new RuntimeException('Cannot chmod log file.');
        }

        // Open the log file for appending.
        $this->filePointer = fopen($this->filename, 'a');

        if ($this->filePointer == false) {
            throw new RuntimeException('Cannot open log file.');
        }

        $this->opened = true;
    }
}
// EOF
