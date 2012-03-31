<?php
namespace Evoke\Core\Writer;
/** XHTML Writing Resource
 *  Provide an interface to the XML Writer to write page content and methods to
 *  write the DTD, head and end of a webpage.
 */
class XML extends XMLBase
{
	/******************/
	/* Public Methods */
	/******************/

	/** Write the DTD, html head and start the body of the document.
	 *  @param setup \array The setup for the start of the document.
	 */
	public function writeStart()
	{
		$this->writeDocInfo('XML');
	}
}
// EOF