<?php
namespace Evoke\Writer;
/** XHTML Writer
 *  Provide an interface to the XML Writer to write page content and methods to
 *  write the DTD, head and end of a webpage.
 */
class XHTML extends XMLBase implements \Evoke\Iface\Writer\Page
{
	/******************/
	/* Public Methods */
	/******************/
		
	/// End the html page and write the output.
	public function writeEnd()
	{
		$this->xmlWriter->endElement(); // body
		$this->xmlWriter->endElement(); // html
	}

	/** Write the DTD, html head and start the body of the document.
	 *  @param setup \array The setup for the start of the document.
	 */
	public function writeStart(Array $setup=array())
	{
		$setup += array('CSS'         => array(),
		                'Description' => '',
		                'Doc_Type'    => 'XHTML_1_1',
		                'Keywords'    => '',
		                'JS'          => array(),
		                'Title'       => '');
		
		$this->writeStartDocument($setup['Doc_Type']);
      
		$this->xmlWriter->startElement('head');
		$this->xmlWriter->writeElement('title', $setup['Title']);
		
		$this->write(array('meta', array('content' => $setup['Title'],
		                                 'name'    => 'title')));
		$this->write(array('meta', array('content' => $setup['Description'],
		                                 'name'    => 'description')));
		$this->write(array('meta', array('content' => $setup['Keywords'],
		                                 'name'    => 'keywords')));
		$this->writeCSS($setup['CSS']);
		$this->writeJS($setup['JS']);
		$this->xmlWriter->endElement(); // head

		$this->xmlWriter->startElement('body');
	}

	/*******************/
	/* Private Methods */
	/*******************/

	/** Write the links to CSS.
	 *  @param cssArr \array Links to the CSS to be written.
	 */
	private function writeCSS($cssArr)
	{
		foreach ($cssArr as $css)
		{
			$this->write(array('link',
			                   array('type' => 'text/css',
			                         'href' => $css,
			                         'rel'  => 'stylesheet')));
		}
	}

	/// Add javascript source reference(s).
	private function writeJS($jsArr)
	{
		foreach($jsArr as $js)
		{
			$this->write(array('script',
			                   array('type' => 'text/javascript',
			                         'src'  => $js)));
		}
	}
}
// EOF