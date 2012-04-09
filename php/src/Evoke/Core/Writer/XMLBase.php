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

	/** @property $XMLWriter
	 *  The XML Writer \object
	 */
	protected $XMLWriter;
	
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

		if (!$setup['XMLWriter'] instanceof \XMLWriter)
		{
			throw new \InvalidArgumentException(
				__METHOD__ . ' requires XMLWriter');
		}
		
		$this->language   = $setup['Language'];
		$this->pos        = $setup['Pos'];
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

		$this->XMLWriter->startElement($tag);

		foreach ($attribs as $attrib => $value)
		{
			$this->XMLWriter->writeAttribute($attrib, $value);
		}

		foreach ($children as $child)
		{
			if (is_string($child))
			{
				$this->XMLWriter->text($child);
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
			$this->XMLWriter->fullEndElement();
		}
		else
		{
			$this->XMLWriter->endElement();
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