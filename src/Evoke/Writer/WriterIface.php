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
 * @author    Paul Young <evoke@youngish.org>
 * @copyright Copyright (c) 2014 Paul Young
 * @license   MIT
 * @package   Writer
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
	public function clean();

	/**
	 * Flush the output buffer (send it and then reset).
	 */
	public function flush();
	
	/**
	 * Write the data into the buffer.
	 *
	 * @param mixed The data to be written.
	 */
	public function write($data);

	/**
	 * End the writing of a document.
	 */
	public function writeEnd();
	
	/**
	 * Start the writing of a document.
	 */
	public function writeStart();
}
// EOF
