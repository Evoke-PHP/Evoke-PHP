<?php
namespace Evoke\Writer;

/**
 * Writer
 *
 * Abstract Writer
 *
 * @author Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license MIT
 * @package Writer
 */
abstract class Writer implements WriterIface
{
	/**
	 * The buffer that holds the text that has been written ready for output.
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
	
	/******************/
	/* Public Methods */
	/******************/

	/**
	 * Get the string representation of the buffer that we are writing to.
	 */
	public function __toString()
	{
		return $this->buffer;
	}

	/**
	 * Reset the buffer that we are writing to.
	 */
	public function flush()
	{
		$this->buffer = '';
	}
	
	/**
	 * Output the buffer that we have written into.
	 */
	public function output()
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
}
// EOF
