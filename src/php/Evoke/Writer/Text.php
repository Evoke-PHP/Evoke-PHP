<?php
/**
 * Text Writer
 *
 * @package Writer
 */
namespace Evoke\Writer;

use InvalidArgumentException;

/**
 * Text Writer
 *
 * Writer for Text (buffered).
 *
 * @author Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license MIT
 * @package Writer
 */
class Text extends Writer
{
	/******************/
	/* Public Methods */
	/******************/
	
	/**
	 * Write text into the buffer.
	 *
	 * @param string The text to write into the buffer.
	 */
	public function write($text)
	{
		if (!is_string($text))
		{
			throw new InvalidArgumentException(
				__METHOD__ . ' text must be a string.');
		}
		
		$this->buffer .= $text;
	}
}
// EOF
