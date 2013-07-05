<?php
/**
 * JSON Writer
 *
 * @package Writer
 */
namespace Evoke\Writer;

/**
 * JSON Writer
 *
 * A buffered writer for JSON.
 *
 * @author    Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license   MIT
 * @package   Writer
 */
class JSON extends Writer
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
	 * Write the data in JSON format into the buffer.
	 *
	 * @param mixed[] PHP data to be encoded into the buffer as JSON.
	 */
	public function write($data)
	{
		$this->buffer .= json_encode($data);
	}
}
// EOF
