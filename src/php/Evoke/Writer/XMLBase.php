<?php
namespace Evoke\Writer;

use InvalidArgumentException,
	XMLWriter;

/**
 * XMLBase
 *
 * Base class for writing XML elements.
 *
 * @author Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license MIT
 * @package Writer
 */
abstract class XMLBase implements WriterIface
{
	/**
	 * The language of the XML being written.
	 * @var string
	 */
	protected $language;
	
	/**
	 * The position of the tag, attribs and children in the element.
	 * @var mixed[]
	 */
	protected $pos;

	/**
	 * The XML Writer object.
	 * @var XMLWriter
	 */
	protected $xmlWriter;
	
	/**
	 * Create an abstract XML Writer.
	 *
	 * @param XMLWriter XMLWriter object.
	 * @param bool      Whether the XML produced should be indented.
	 * @param string    The string that should be used to indent the XML.
	 * @param string    The language of the XML we are writing.
	 * @param mixed[]   The positions of the components within the XML.
	 */
	public function __construct(
		XMLWriter    $xmlWriter,
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

	/**
	 * Get the XHTML that has been written into the memory buffer (without
	 * resetting it).
	 *
	 * @return string The XHTML from the buffer as a string.
	 */
	public function __toString()
	{
		return $this->xmlWriter->outputMemory(false);
	}
   
	/**
	 * Flush the memory buffer containing the XHTML that has been written.
	 */
	public function flush()
	{
		$this->xmlWriter->flush();
	}
	
	/**
	 * Write XML elements into the memory buffer.
	 *
	 * @param mixed[] Array accessible value for the xml to be written of the
	 *                form: `array($tag, $attributes, $children)`
	 *
	 * An example of this is below with the default values that are used for the
	 * options array. Attributes and options are optional.
	 * <pre><code>
	 * array(0 => tag,
	 *       1 => array('attrib_1' => '1', 'attrib_2' => '2'),
	 *       2 => array($child, 'text', $anotherChild)
	 *      )
	 * </code></pre>
	 */
	public function write($xml)
	{
		if (empty($xml[$this->pos['Tag']]) ||
		    !is_string($xml[$this->pos['Tag']]))
		{
			throw new InvalidArgumentException(
				__METHOD__ . ' bad tag: ' . var_export($xml, true));
		}

		if (isset($xml[$this->pos['Attribs']]) &&
		    !is_array($xml[$this->pos['Attribs']]))
		{
			throw new InvalidArgumentException(
				__METHOD__ . ' bad attributes: ' . var_export($xml, true));
		}

		if (isset($xml[$this->pos['Children']]))
		{
			if (!is_array($xml[$this->pos['Children']]))
			{
				$xml[$this->pos['Children']]
					= array($xml[$this->pos['Children']]);
			}
		}
			
		$tag      = $xml[$this->pos['Tag']];
		$attribs  = isset($xml[$this->pos['Attribs']]) ?
			$xml[$this->pos['Attribs']] : array();
		$children = isset($xml[$this->pos['Children']]) ?
			$xml[$this->pos['Children']] : array();

		$inlineElement = (preg_match('(^(strong|em|pre|code)$)i', $tag));

		// Toggle the indent off.
		if ($inlineElement)
		{
			$this->xmlWriter->setIndent(false);
		}
		
		$this->xmlWriter->startElement($tag);

		foreach ($attribs as $attrib => $value)
		{
			$this->xmlWriter->writeAttribute($attrib, $value);
		}

		foreach ($children as $child)
		{
			if (is_scalar($child))
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

		// Toggle the indent back on.
		if ($inlineElement)
		{
			$this->xmlWriter->setIndent(true);
		}
	}

	/*********************/
	/* Protected Methods */
	/*********************/
	
	/**
	 * Write the start of the document based on the type.
	 *
	 * @param string The basic doc type ('XHTML5', 'XHTML_1_1', 'XML').
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