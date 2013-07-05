<?php
/**
 * XHTML Writer
 *
 * @package Writer
 */
namespace Evoke\Writer;

/**
 * XHTML Writer
 *
 * Provide an interface to write XHTML specific content.
 *
 * @author    Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license   MIT
 * @package   Writer
 */
class XHTML extends XMLBase
{
	/**
	 * Protected Properties
	 *
	 * @var string   $description Description of the page.
	 * @var string[] $headCSS     CSS source files for the document head.
	 * @var string[] $headJS      Javascript source files for the document head.
	 * @var string   $keywords    Keywords of the page.
	 * @var string   $title       Title of the page.
	 */
	protected $description = '', $headCSS = array(), $headJS = array(),
		$keywords = '', $title = '';
	
	/******************/
	/* Public Methods */
	/******************/

	/**
	 * Add the CSS source file to the list of items to be written in the
	 * document head.
	 *
	 * @param string CSS source file.
	 */
	public function addCSS($source)
	{
		$this->headCSS[] = (string)$source;
	}

	/**
	 * Add the JS source file to the list of items to be written in the
	 * document head.
	 *
	 * @param string JS source file.
	 */
	public function addJS($source)
	{
		$this->headJS[] = (string)$source;
	}

	/**
	 * Set the description for the page.
	 *
	 * @param string Description for the page.
	 */
	public function setDescription($description)
	{
		$this->description = (string)$description;
	}

	/**
	 * Set the keywords for the page.
	 *
	 * @param string Keywords for the page.
	 */
	public function setKeywords($keywords)
	{
		$this->keywords = (string)$keywords;
	}
	
	/**
	 * Set the title for the page.
	 *
	 * @param string Title for the page.
	 */
	public function setTitle($title)
	{
		$this->title = (string)$title;
	}	
	
	/**
	 * End the html page.
	 */
	public function writeEnd()
	{
		$this->xmlWriter->endElement(); // body
		$this->xmlWriter->endElement(); // html
	}

	/**
	 * Write the DTD, html head and start the body of the document.
	 */
	public function writeStart()
	{
		$this->writeStartDocument();
      
		$this->xmlWriter->startElement('head');
		$this->xmlWriter->writeElement('title', $this->title);
		
		$this->write(array('meta', array('content' => $this->title,
		                                 'name'    => 'title')));
		$this->write(array('meta', array('content' => $this->description,
		                                 'name'    => 'description')));
		$this->write(array('meta', array('content' => $this->keywords,
		                                 'name'    => 'keywords')));
		$this->writeCSS($this->headCSS);
		$this->writeJS($this->headJS);
		$this->xmlWriter->endElement(); // head

		$this->xmlWriter->startElement('body');
	}

	/*******************/
	/* Private Methods */
	/*******************/

	/**
	 * Write the links to CSS.
	 *
	 * @param string[] Links to the CSS to be written.
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

	/**
	 * Add javascript source reference(s).
	 *
	 * @param string[] Javascript files.
	 */
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