<?php
namespace Evoke\Core\Writer;

/// Base class for writing XML elements.
abstract class XMLBase implements \Evoke\Core\Iface\Writer
{
	/** @property $attribsPos
	 *  \int The position of the attributes in the XML arrays being written.
	 */
	protected $attribsPos;

	/** @property $language
	 *  \string The language of the XML being written.
	 */
	protected $language;

	/** @property $optionsPos
	 *  \int The position of the options in the XML arrays being written.
	 */
	protected $optionsPos;

	/** @property $tagPos
	 *  \int The position of the tag in the XML arrays being written.
	 */
	protected $tagPos;

	/** @property $XMLWriter
	 *  The XML Writer \object
	 */
	protected $XMLWriter;
	
	/** Create an abstract XML Writer.
	 *  @param setup \array The setup for the XML Writer.
	 */
	public function __construct(Array $setup=array())
	{
		$setup += array('Attribs_Pos'   => 1,
		                'Indent'        => true,
		                'Indent_String' => '   ',
		                'Language'      => 'EN',
		                'Options_Pos'   => 2,
		                'Tag_Pos'       => 0,
		                'XMLWriter'     => NULL);

		if (!$setup['XMLWriter'] instanceof \XMLWriter)
		{
			throw new \InvalidArgumentException(
				__METHOD__ . ' requires XMLWriter');
		}
		
		$this->attribsPos = $setup['Attribs_Pos'];
		$this->language   = $setup['Language'];
		$this->optionsPos = $setup['Options_Pos'];
		$this->tagPos     = $setup['Tag_Pos'];
		$this->XMLWriter  = $setup['XMLWriter'];

		$this->XMLWriter->openMemory();
			
		if ($setup['Indent'])
		{
			$this->XMLWriter->setIndentString($setup['Indent_String']);
			$this->XMLWriter->setIndent(true);
		}
	}
	
	/******************/
	/* Public Methods */
	/******************/

	/** Get the XHTML that has been written into the memory buffer (without
	 *  resetting it).
	 *  \return A string of the XHTML.
	 */
	public function __toString()
	{
		return $this->XMLWriter->outputMemory(false);
	}
   
	/// Flush the memory buffer containing the XHTML that has been written.
	public function flush()
	{
		$this->XMLWriter->flush();
	}
	
	/** Output the memory buffer for the XHTML that has been written and reset
	 *  the memory buffer.
	 */
	public function output()
	{
		echo($this->XMLWriter->outputMemory(true));
	}

	/** Write XML elements into the memory buffer.
	 *  @param xml \mixed Array accessible value for the xml to be written of the
	 *  form: array($tag, $attributes, $options)
	 *
	 *  An example of this is below with the default values that are used for the
	 *  options array. Attributes and options are optional.
	 *  \verbatim
	 *  array(0 => tag,
	 *        1 => array('attrib_1' => '1', 'attrib_2' => '2'),
	 *        2 => array('Children' => array(), // Child elements within the tag.
	 *                   'Finish'   => true,    // Whether to end the tag.
	 *                   'Start'    => true,    // Whether to start the tag.
	 *                   'Text'     => NULL),   // Text within the tag.
	 *       )
	 *  \endverbatim
	 */
	public function write($xml)
	{
		if (!isset($xml[$this->tagPos]))
		{
			throw new \InvalidArgumentException(
				__METHOD__ . ' Bad element: ' . var_export($xml, true));
		}

		$tag = $xml[$this->tagPos];
		$attribs = array();
		$options = array('Children' => array(),
		                 'Finish'   => true,  
		                 'Start'    => true,
		                 'Text'     => NULL);

		if (isset($xml[$this->attribsPos]))
		{
			$attribs = $xml[$this->attribsPos];
		}

		if (isset($xml[$this->optionsPos]))
		{
			$options = array_merge($options, $xml[$this->optionsPos]);
		}
      
		if (!empty($tag) && $options['Start'])
		{
			$this->XMLWriter->startElement($tag);
		}

		foreach ($attribs as $attrib => $value)
		{
			$this->XMLWriter->writeAttribute($attrib, $value);
		}

		if (isset($options['Text']))
		{
			$text = (string)($options['Text']);

			// If we have children insert a newline to ensure correct indentation.
			if (!empty($options['Children']))
			{
				$text .= "\n";
			}
	 
			$this->XMLWriter->text($text);
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
				$this->XMLWriter->fullEndElement();
			}
			else
			{
				$this->XMLWriter->endElement();
			}
		}
	}

	/*********************/
	/* Protected Methods */
	/*********************/
	
	/** Write the start of the document based on the type.
	 *  @param type \string The basic doc type ('XHTML5', 'XHTML_1_1', 'XML').
	 */
	protected function writeStartDocument($type)
	{
		switch (strtoupper($type))
		{
		case 'XHTML5':
			$this->XMLWriter->startDTD('html');
			$this->XMLWriter->endDTD();
			break;

		case 'XML':
			$this->XMLWriter->startDocument('1.0', 'UTF-8');
			break;
				
		case 'XHTML_1_1':
		default:
			$this->XMLWriter->startDTD(
				'html',
				'-//W3C//DTD XHTML 1.1//EN',
				'http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd');
			$this->XMLWriter->endDTD();

			$this->XMLWriter->startElementNS(
				null, 'html', 'http://www.w3.org/1999/xhtml');
			$this->XMLWriter->writeAttribute('lang', $this->language);
			$this->XMLWriter->writeAttribute('xml:lang', $this->language);
			break;
		}
	}
}
// EOF