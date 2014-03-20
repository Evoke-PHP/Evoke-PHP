<?php
/**
 * Abstract Writer
 *
 * @package Writer
 */
namespace Evoke\Writer;

/**
 * Abstract Writer
 *
 * @author    Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license   MIT
 * @package   Writer
 */
abstract class Writer implements WriterIface
{
	/**
	 * The buffer that holds the text that is to be written.
	 * @var string
	 */
	protected $buffer;

	/**
	 * Construct the buffered Writer.
	 */
	public function __construct()
	{
		$this->buffer = '';
	}

	/**
	 * Get the string representation of the buffer that we are writing to.
	 */
	public function __toString()
	{
		return $this->buffer;
	}

	/******************/
	/* Public Methods */
	/******************/

	/**
	 * Reset the buffer that we are writing to.
	 */
	public function clean()
	{
		$this->buffer = '';
	}

	/**
	 * Flush the output buffer (send it and then reset).
	 */
	public function flush()
	{
		echo $this->buffer;
		$this->buffer = '';
	}
		
	/**
	 * Write data into the buffer.
	 *
	 * @param mixed The data to write into the buffer.
	 */
	abstract public function write($data);

	/**
	 * By default nothing is required to be initialized on write start.
	 */
	public function writeStart()
	{

	}
}
// EOF
