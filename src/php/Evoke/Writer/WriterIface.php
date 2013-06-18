<?php
/**
 * Writer Interface
 *
 * @package Writer
 */
namespace Evoke\Writer;

/**
 * Writer Interface
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
	 * Write the data into the buffer.
	 *
	 * @param mixed The data to be written.
	 */
	public function write($data);
}
// EOF
