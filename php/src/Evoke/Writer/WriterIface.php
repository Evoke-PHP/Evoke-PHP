<?php
namespace Evoke\Writer;

/**
 * WriterIface
 *
 * The interface to an object that writes (using a buffer).
 *
 * @author Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license MIT
 * @package Writer
 */
interface WriterIface
{
	/**
	 * Get the string representation of the buffer that we are writing to.
	 */
	public function __toString();

	/**
	 * Reset the buffer that we are writing to.
	 */
	public function flush();

	/**
	 * Output the buffer that we have written into.
	 */
	public function output();
	
	/**
	 * Write the data into the buffer.
	 *
	 * @param mixed The data to be written.
	 */
	public function write($data);
}
// EOF
