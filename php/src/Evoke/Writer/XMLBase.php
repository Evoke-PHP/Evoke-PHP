<?php
namespace Evoke\Writer;

/// Base class for writing XML elements.
abstract class XMLBase implements \Evoke\Iface\Writer
{
	/** @property $language
	 *  @string The language of the XML being written.
	 */
	protected $language;
	
	/** @property $pos
	 *  @array The position of the tag, attribs and children in the element.
	 */
	protected $pos;

	/** @property $xMLWriter
	 *  @object The XML Writer 
	 */
	protected $xmlWriter;
	
	/** Create an abstract XML Writer.
	 *  @param xmlWriter    @object XMLWriter object.
	 *  @param indent       @bool   Whether the XML produced should be indented.
	 *  @param indentString @string The string that should be used to indent
	 *                              the XML.
	 *  @param language     @string The language of the XML we are writing.
	 *  @param pos          @array  The positions of the components within the
	 *                              XML.
	 */
	public function __construct(
		\XMLWriter   $xmlWriter,
		/* Bool */   $indent       = true,
		/* String */ $indentString = '   ',
		/* String */ $language     = 'EN',
		Array        $pos          = array('Attribs'  => 1,
		                                   'Children' => 2,
		                                   'Tag'      => 0))
	{
		$this->language   = $language;
		$this->pos        = $pos;
		$this->xmlWriter  = $xmlWriter;

		$this->xmlWriter->openMemory();
			
		if ($indent)
		{
			$this->xmlWriter->setIndentString($indentString);
			$this->xmlWriter->setIndent(true);
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
		return $this->xmlWriter->outputMemory(false);
	}
   
	/// Flush the memory buffer containing the XHTML that has been written.
	public function flush()
	{
		$this->xmlWriter->flush();
	}
	
	/** Output the memory buffer for the XHTML that has been written and reset
	 *  the memory buffer.
	 */
	public function output()
	{
		echo($this->xmlWriter->outputMemory(true));
	}

	/** Write XML elements into the memory buffer.
	 *  @param xml \mixed Array accessible value for the xml to be written of the
	 *  form: array($tag, $attributes, $children)
	 *
	 *  An example of this is below with the default values that are used for the
	 *  options array. Attributes and options are optional.
	 *  \verbatim
	 *  array(0 => tag,
	 *        1 => array('attrib_1' => '1', 'attrib_2' => '2'),
	 *        2 => array($child, 'text', $anotherChild)
	 *       )
	 *  \endverbatim
	 */
	public function write($xml)
	{
		if (empty($xml[$this->pos['Tag']]) ||
		    !is_string($xml[$this->pos['Tag']]))
		{
			throw new \InvalidArgumentException(
				__METHOD__ . ' bad tag: ' . var_export($xml, true));
		}

		if (isset($xml[$this->pos['Attribs']]) &&
		    !is_array($xml[$this->pos['Attribs']]))
		{
			throw new \InvalidArgumentException(
				__METHOD__ . ' bad attributes: ' . var_export($xml, true));
		}

		if (isset($xml[$this->pos['Children']]))
		{
			if (is_string($xml[$this->pos['Children']]))
			{
				$xml[$this->pos['Children']]
					= array($xml[$this->pos['Children']]);
			}
			elseif (!is_array($xml[$this->pos['Children']]))
			{
				throw new \InvalidArgumentException(
					__METHOD__ . ' bad children: ' . var_export($xml, true));
			}
		}
			
		$tag      = $xml[$this->pos['Tag']];
		$attribs  = isset($xml[$this->pos['Attribs']]) ?
			$xml[$this->pos['Attribs']] : array();
		$children = isset($xml[$this->pos['Children']]) ?
			$xml[$this->pos['Children']] : array();

		$this->xmlWriter->startElement($tag);

		foreach ($attribs as $attrib => $value)
		{
			$this->xmlWriter->writeAttribute($attrib, $value);
		}

		foreach ($children as $child)
		{
			if (is_string($child))
			{
				$this->xmlWriter->text($child);
			}
			else
			{
				$this->write($child);
			}
		}

		// Some elements should always have a full end tag <div></div> rather
		// than <div/>
		if (preg_match('(^(div|script|textarea)$)i', $tag))
		{
			$this->xmlWriter->fullEndElement();
		}
		else
		{
			$this->xmlWriter->endElement();
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
			$this->xmlWriter->startDTD('html');
			$this->xmlWriter->endDTD();
			break;

		case 'XML':
			$this->xmlWriter->startDocument('1.0', 'UTF-8');
			break;
				
		case 'XHTML_1_1':
		default:
			$this->xmlWriter->startDTD(
				'html',
				'-//W3C//DTD XHTML 1.1//EN',
				'http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd');
			$this->xmlWriter->endDTD();

			$this->xmlWriter->startElementNS(
				null, 'html', 'http://www.w3.org/1999/xhtml');
			$this->xmlWriter->writeAttribute('lang', $this->language);
			$this->xmlWriter->writeAttribute('xml:lang', $this->language);
			break;
		}
	}
}
// EOF