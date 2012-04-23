<?php
namespace Evoke\Core\Writer;

/// Base class for writing XML elements.
abstract class XMLBase implements \Evoke\Core\Iface\Writer
{

	/** @property $language
	 *  \string The language of the XML being written.
	 */
	protected $language;
	
	/** @property $pos
	 *  \array The position of the tag, attribs and children in the element.
	 */
	protected $pos;

	/** @property $xMLWriter
	 *  The XML Writer \object
	 */
	protected $xMLWriter;
	
	/** Create an abstract XML Writer.
	 *  @param setup \array The setup for the XML Writer.
	 */
	public function __construct(Array $setup=array())
	{
		$setup += array('Indent'        => true,
		                'Indent_String' => '   ',
		                'Language'      => 'EN',
		                'Pos'           => array('Attribs'  => 1,
		                                         'Children' => 2,
		                                         'Tag'      => 0),
		                'XMLWriter'     => NULL);

		if (!$xMLWriter instanceof \XMLWriter)
		{
			throw new \InvalidArgumentException(
				__METHOD__ . ' requires XMLWriter');
		}
		
		$this->language   = $language;
		$this->pos        = $pos;
		$this->xMLWriter  = $xMLWriter;

		$this->xMLWriter->openMemory();
			
		if ($indent)
		{
			$this->xMLWriter->setIndentString($indentString);
			$this->xMLWriter->setIndent(true);
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
		return $this->xMLWriter->outputMemory(false);
	}
   
	/// Flush the memory buffer containing the XHTML that has been written.
	public function flush()
	{
		$this->xMLWriter->flush();
	}
	
	/** Output the memory buffer for the XHTML that has been written and reset
	 *  the memory buffer.
	 */
	public function output()
	{
		echo($this->xMLWriter->outputMemory(true));
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

		$this->xMLWriter->startElement($tag);

		foreach ($attribs as $attrib => $value)
		{
			$this->xMLWriter->writeAttribute($attrib, $value);
		}

		foreach ($children as $child)
		{
			if (is_string($child))
			{
				$this->xMLWriter->text($child);
			}
			else
			{
				$this->write($child);
			}
		}

		// Some elements should always have a full end tag <div></div> rather
		// than <div/>
		if (preg_match('(^(div|link|script|textarea)$)i', $tag))
		{
			$this->xMLWriter->fullEndElement();
		}
		else
		{
			$this->xMLWriter->endElement();
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
			$this->xMLWriter->startDTD('html');
			$this->xMLWriter->endDTD();
			break;

		case 'XML':
			$this->xMLWriter->startDocument('1.0', 'UTF-8');
			break;
				
		case 'XHTML_1_1':
		default:
			$this->xMLWriter->startDTD(
				'html',
				'-//W3C//DTD XHTML 1.1//EN',
				'http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd');
			$this->xMLWriter->endDTD();

			$this->xMLWriter->startElementNS(
				null, 'html', 'http://www.w3.org/1999/xhtml');
			$this->xMLWriter->writeAttribute('lang', $this->language);
			$this->xMLWriter->writeAttribute('xml:lang', $this->language);
			break;
		}
	}
}
// EOF