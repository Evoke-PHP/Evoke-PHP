<?php
/**
 * HTML5 Writer
 *
 * @package Writer
 */
namespace Evoke\Writer;

/**
 * HTML5 Writer
 *
 * Write HTML5 specific content.
 *
 * @author    Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license   MIT
 * @package   Writer
 */
class HTML5 extends XHTML
{
	/******************/
	/* Public Methods */
	/******************/
		
	/**
	 * Write the DTD, html head and start the body of the document.
	 *
	 * @param mixed[] The setup for the start of the document.
	 */
	public function writeStart(Array $setup=array())
	{
		$setup += array('Doc_Type' => 'HTML5');
		parent::writeStart($setup);
	}
}
// EOF