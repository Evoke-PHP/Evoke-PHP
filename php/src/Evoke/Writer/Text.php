<?php
namespace Evoke\Writer;

/** Writer for Text (buffered).
 */
class Text extends Base
{
	/******************/
	/* Public Methods */
	/******************/
	
	/** Write text into the buffer.
	 *  @param text @string The text to write into the buffer.
	 */
	public function write($text)
	{
		if (!is_string($text))
		{
			throw new \InvalidArgumentException(
				__METHOD__ . ' text must be a string.');
		}
		
		$this->buffer .= $text;
	}
}
// EOF
