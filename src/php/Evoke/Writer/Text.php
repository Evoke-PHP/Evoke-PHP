<?php
/**
 * Text Writer
 *
 * @package Writer
 */
namespace Evoke\Writer;

/**
 * Text Writer
 *
 * Writer for Text (buffered).
 *
 * @author    Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license   MIT
 * @package   Writer
 */
class Text extends Writer
{
	/******************/
	/* Public Methods */
	/******************/

	/**
	 * Whether the writer is page based or not.
	 *
	 * @return bool Whether the writer is page based.
	 */
	public function isPageBased()
	{
		return FALSE;
	}
	
	/**
	 * Write text into the buffer.
	 *
	 * @param string The text to write into the buffer.
	 */
	public function write($text)
	{
		$this->buffer .= $text;
	}
}
// EOF
