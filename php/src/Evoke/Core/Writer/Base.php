<?php
namespace Evoke\Core\Writer;

/// abstract Writer.
abstract class Base implements \Evoke\Iface\Core\Writer
{
	/** @property $buffer
	 *  @string The buffer that holds the text that has been written ready for
	 *  output.
	 */
	protected $buffer;

	/// Construct the buffered Writer.
	public function __construct()
	{
		$this->buffer = '';
	}
	
	/******************/
	/* Public Methods */
	/******************/

	/// Get the string representation of the buffer that we are writing to.
	public function __toString()
	{
		return $this->buffer;
	}

	/// Reset the buffer that we are writing to.
	public function flush()
	{
		$this->buffer = '';
	}
	
	/// Output the buffer that we have written into.
	public function output()
	{
		echo $this->buffer;
		$this->buffer = '';
	}
	
	/** Write data into the buffer.
	 *  @param data @mixed The data to write into the buffer.
	 */
	abstract public function write($data);
}
// EOF
