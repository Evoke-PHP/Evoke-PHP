<?php
namespace Evoke\Writer;

/** Writer for JSON (buffered).
 */
class JSON extends \Evoke\Writer
{	
	/******************/
	/* Public Methods */
	/******************/

	/** Write the JSON data into the buffer.
	 *  @param data \mixed PHP data to be converted to JSON for writing.
	 */
	public function write($data)
	{
		$this->buffer .= json_encode($data);
	}
}
// EOF
