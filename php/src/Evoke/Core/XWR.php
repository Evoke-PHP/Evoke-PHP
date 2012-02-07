<?php
namespace Evoke\Core;
/** XHTML Writing Resource
 *  Provide an interface to the XML Writer to write page content and methods to
 *  write the DTD, head and end of a webpage.
 */
class XWR extends \XMLWriter
{
	protected $setup;
   
	/// Create the interface to writing our XHTML.
	public function __construct($setup=array())
	{
		$this->setup = array_merge(
			array('Attribs_Pos' => 1,
			      'Language'    => 'EN',
			      'Options_Pos' => 2,
			      'Tag_Pos'     => 0),
			$setup);
      
		$this->openMemory();
		$this->setIndentString('   ');
		$this->setIndent(true);
	}

	/******************/
	/* Public Methods */
	/******************/
   
	/** Get the XHTML that has been created.
	 *  \return A string of the XHTML.
	 */
	public function get()
	{
		return $this->outputMemory(false);
	}
   
	/// Output the XHTML that has been created.
	public function output()
	{
		echo($this->outputMemory(true));
	}

	/** Write XML elements into the current document.
	 *  @param xml \array An array holding the xml to be written of the form:
	 *     array($tag, $attributes, $options)
	 *  An example of this is below with the default values that are used for the
	 *  options array. Attributes and options are optional.
	 *  \verbatim
	 array(0 => tag,
	 1 => array('attrib_1' => '1', 'attrib_2' => '2'),
	 2 => array('Children' => array(), // Child elements within the tag.
	 'Finish'   => true,    // Whether to end the tag.
	 'Start'    => true,    // Whether to start the tag.
	 'Text'     => NULL),   // Text within the tag.
	 )
	 \endverbatim
	*/
	public function write($xml)
	{
		if (!isset($xml[$this->setup['Tag_Pos']]))
		{
			throw new \InvalidArgumentException(
				__METHOD__ . ' Bad element: ' . var_export($xml, true));
		}

		$tag = $xml[$this->setup['Tag_Pos']];
		$attribs = array();
		$options = array('Children' => array(),
		                 'Finish'   => true,  
		                 'Start'    => true,
		                 'Text'     => NULL);

		if (isset($xml[$this->setup['Attribs_Pos']]))
		{
			$attribs = $xml[$this->setup['Attribs_Pos']];
		}

		if (isset($xml[$this->setup['Options_Pos']]))
		{
			$options = array_merge($options, $xml[$this->setup['Options_Pos']]);
		}
      
		if (!empty($tag) && $options['Start'])
		{
			$this->startElement($tag);
		}

		foreach ($attribs as $attrib => $value)
		{
			$this->writeAttribute($attrib, $value);
		}

		if (isset($options['Text']))
		{
			$text = (string)($options['Text']);

			// If we have children insert a newline to ensure correct indentation.
			if (!empty($options['Children']))
			{
				$text .= "\n";
			}
	 
			$this->text($text);
		}

		if (!is_array($options['Children']))
		{
			throw new \RuntimeException(
				__METHOD__ . ' Children must be passed as an array not: ' .
				var_export($options['Children'], true));
		}
      
		foreach ($options['Children'] as $key => $child)
		{
			try
			{
				$this->write($child); // Recursively write children.
			}
			catch (\Exception $e)
			{
				$msg = 'Element with Tag: ' . var_export($tag, true);

				if (!empty($attribs))
				{
					$msg .= ' Attribs: ' . var_export($attribs, true);
				}

				if (!empty($options['Text']))
				{
					$msg .= ' Text: ' . var_export($options['Text'], true);
				}

				$msg .= ' contains invalid child at index ' .
					var_export($key, true) . ': ';

				if (is_object($child))
				{
					$msg .= get_class($child);
				}
				else
				{
					$msg .= var_export($child, true);
				}

				throw new \RuntimeException(__METHOD__ . $msg, 0, $e);
			}
		}

		if (!empty($tag) && $options['Finish'])
		{
			$tagStr = strtoupper($tag);

			// Some elements should always have a full end tag <div></div> rather
			// than <div/>
			if ($tagStr === 'DIV' || $tagStr === 'LINK' || $tagStr === 'SCRIPT' ||
			    $tagStr === 'TEXTAREA')
			{
				$this->fullEndElement();
			}
			else
			{
				$this->endElement();
			}
		}
	}

	/// End the html page and write the output.
	public function writeEnd()
	{
		$this->endElement(); // body
		$this->endElement(); // html
	}

	/// Write the DTD, html head and start the body of the document.
	public function writeStart($setup=array())
	{
		$setup = array_merge(array('CSS' => array(),
		                           'Description' => '',
		                           'DTD' => 'XHTML_1_1',
		                           'Keywords' => '',
		                           'Lang' => '',
		                           'JS' => array(),
		                           'Title' => ''),
		                     $setup);
      
		$this->writeDTD($setup['DTD'], '', '', '', $setup['Lang']);
      
		$this->startElement('head');
		$this->writeElement('title', $setup['Title']);
		$this->writeMeta('description', $setup['Description']);
		$this->writeMeta('keywords', $setup['Keywords']);
		$this->writeCSS($setup['CSS']);
		$this->writeJS($setup['JS']);
		$this->endElement(); // head

		$this->startElement('body');
	}

	/*******************/
	/* Private Methods */
	/*******************/

	/// Add link(s) to CSS stylesheet(s).
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

	/// Write the DTD.
	public function writeDTD(
		$name='XHTML_1_1', $publicId='', $systemId='', $subset='', $lang='')
	{
		if (empty($lang))
		{	    
			$lang = $this->setup['Language'];
		}
      
		switch ($name)
		{
		case 'XHTML_1_1':
		default:
			$this->startDTD('html', '-//W3C//DTD XHTML 1.1//EN',
			                'http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd');
			$this->endDTD();
	 
			$this->startElementNS(null, 'html', 'http://www.w3.org/1999/xhtml');
			$this->writeAttribute('lang', $lang);
			$this->writeAttribute('xml:lang', $lang);
			break;
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

	/// Write meta information to the document.
	private function writeMeta($name, $content)
	{
		$this->startElement('meta');
		$this->writeAttribute('name', $name);
		$this->writeAttribute('content', $content);
		$this->endElement();

	}
}
// EOF