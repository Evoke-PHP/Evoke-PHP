<?php
/**
 * XML Writer
 *
 * @package Writer
 */
namespace Evoke\Writer;
/**
 * XML Writer
 *
 * Provide an interface to write XML specific content.
 * 
 * @author    Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license   MIT
 * @package   Writer
 */
class XML extends XMLBase
{
	/******************/
	/* Public Methods */
	/******************/

	/**
	 * Write the end of the document.
	 */
	public function writeEnd()
	{
		$this->xmlWriter->endDocument();
	}
	
	/**
	 * Write the start of the document.
	 */
	public function writeStart()
	{
		$this->writeStartDocument('XML');
	}
}
// EOF