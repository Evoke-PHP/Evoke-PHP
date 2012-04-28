<?php
namespace Evoke\Iface;

/// The interface to an object that writes (using a buffer).
interface Writer
{
	/// Get the string representation of the buffer that we are writing to.
	public function __toString();

	/// Reset the buffer that we are writing to.
	public function flush();

	/// Output the buffer that we have written into.
	public function output();
	
	/// Write the data into the buffer.
	public function write($data);
}
// EOF
