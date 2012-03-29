<?php
namespace Evoke\Core\Writer;

/** Writer for JSON (buffered).
 */
class JSON implements \Evoke\Core\Iface\Writer
{
	protected $buffer;

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

	/// Output the buffer that we have written into.
	public function output()
	{
		echo $this->buffer;
		$this->buffer = '';
	}
	
	/// Write the data into the buffer.
	public function write($data)
	{
		$this->buffer .= json_encode($data);
	}
}
// EOF
