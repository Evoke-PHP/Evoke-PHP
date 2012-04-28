<?php
namespace Evoke\Writer;
/** HTML5 Writer
 *  Provide an interface to the XML Writer to write page content and methods to
 *  write the DTD, head and end of a webpage.
 */
class HTML5 extends XHTML
{
	/******************/
	/* Public Methods */
	/******************/
		
	/** Write the DTD, html head and start the body of the document.
	 *  @param setup \array The setup for the start of the document.
	 */
	public function writeStart(Array $setup=array())
	{
		$setup += array('Doc_Type' => 'HTML5');
		parent::writeStart($setup);
	}
}
// EOF