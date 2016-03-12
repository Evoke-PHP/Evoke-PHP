<?php
/**
 * XML Writer
 *
 * @package Writer
 */
namespace Evoke\Writer;

use DomainException;
use InvalidArgumentException;
use XMLWriter;

/**
 * XML Writer
 *
 * Writer for XML elements.
 *
 * @author    Paul Young <evoke@youngish.org>
 * @copyright Copyright (c) 2015 Paul Young
 * @license   MIT
 * @package   Writer
 */
class XML implements WriterIface
{
    /**
     * Document type.
     * @var string
     */
    protected $docType;

    /**
     * Whether we indent non-inline elements.
     * @var bool
     */
    protected $indent;

    /**
     * Language of XML being written.
     * @var string
     */
    protected $language;

    /**
     * Position of the tag, attribs and children in the element.
     * @var mixed[]
     */
    protected $pos;

    /**
     * XML Writer object.
     * @var XMLWriter
     */
    protected $xmlWriter;

    /**
     * Create an XML Writer.
     *
     * @param XMLWriter $xmlWriter    XMLWriter object.
     * @param string    $docType      Document Type.
     * @param string    $language     Language of XML being written.
     * @param bool      $indent       Whether the XML produced should be indented.
     * @param string    $indentString The string that should be used to indent the XML.
     * @param int[]     $pos          Position of the tag, attribs & children in the element.
     */
    public function __construct(
        XMLWriter $xmlWriter,
        $docType = 'XHTML_1_1',
        $language = 'EN',
        $indent = true,
        $indentString = '   ',
        $pos = [
            'attribs'  => 1,
            'children' => 2,
            'tag'      => 0
        ]
    ) {
        $this->docType   = $docType;
        $this->indent    = $indent;
        $this->language  = $language;
        $this->pos       = $pos;
        $this->xmlWriter = $xmlWriter;

        $this->xmlWriter->openMemory();

        if ($indent) {
            $this->xmlWriter->setIndentString($indentString);
            $this->xmlWriter->setIndent(true);
        }
    }

    /******************/
    /* Public Methods */
    /******************/

    /**
     * Get the XHTML that has been written into the memory buffer (without resetting it).
     *
     * @return string The XHTML from the buffer as a string.
     */
    public function __toString()
    {
        return $this->xmlWriter->outputMemory(false);
    }

    /**
     * Reset the buffer that we are writing to.
     */
    public function clean()
    {
        $this->xmlWriter->outputMemory(true);
    }

    /**
     * Flush the memory buffer containing the XHTML that has been written.
     */
    public function flush()
    {
        echo $this->xmlWriter->outputMemory(true);
    }

    /**
     * Write XML elements into the memory buffer.
     *
     * @param mixed[] $xml
     * Array accessible value for the xml to be written of the form: `[$tag, $attributes, $children]`
     *
     * An example of this is below with the default values that are used for the options array. Attributes and options
     * are optional.
     * <pre><code>
     * [0 => tag,
     *  1 => ['attrib_1' => '1', 'attrib_2' => '2'],
     *  2 => [$child, 'text', $anotherChild]]
     * </code></pre>
     *
     * @throws InvalidArgumentException for bad xml data.
     */
    public function write($xml)
    {
        if (empty($xml[$this->pos['tag']]) || !is_string($xml[$this->pos['tag']])) {
            throw new InvalidArgumentException('bad tag: ' . var_export($xml, true));
        }

        if (isset($xml[$this->pos['attribs']]) && !is_array($xml[$this->pos['attribs']])) {
            throw new InvalidArgumentException('bad attributes: ' . var_export($xml, true));
        }

        if (isset($xml[$this->pos['children']]) && !is_array($xml[$this->pos['children']])) {
            $xml[$this->pos['children']] = [$xml[$this->pos['children']]];
        }

        $tag      = $xml[$this->pos['tag']];
        $attribs  = isset($xml[$this->pos['attribs']]) ? $xml[$this->pos['attribs']] : [];
        $children = isset($xml[$this->pos['children']]) ? $xml[$this->pos['children']] : [];

        // Whether we are normally indenting and we see an element that should be inline.
        $specialInlineElement = ($this->indent && preg_match('(^(strong|em|pre|code)$)i', $tag));

        // Toggle the indent off.
        if ($specialInlineElement) {
            $this->xmlWriter->setIndent(false);
        }

        $this->xmlWriter->startElement($tag);

        foreach ($attribs as $attrib => $value) {
            $this->xmlWriter->writeAttribute($attrib, $value);
        }

        foreach ($children as $child) {
            if (is_scalar($child)) {
                $this->xmlWriter->text($child);
            } elseif (!is_null($child)) {
                $this->write($child);
            }
        }

        // Some elements should always have a full end tag <div></div> rather than <div/>
        if (preg_match('(^(div|iframe|script|span|textarea)$)i', $tag)) {
            $this->xmlWriter->fullEndElement();
        } else {
            $this->xmlWriter->endElement();
        }

        if ($specialInlineElement) {
            // Toggle the indent back on.
            $this->xmlWriter->setIndent(true);
        }
    }

    /**
     * Write the End of the document.
     */
    public function writeEnd()
    {
        $this->xmlWriter->endDocument();
    }

    /**
     * Write the start of the document based on the doc type.
     *
     * @throws DomainException For an unknown document type.
     */
    public function writeStart()
    {
        switch (strtoupper($this->docType)) {
            case 'HTML5':
                $this->xmlWriter->startDTD('html');
                $this->xmlWriter->endDTD();
                $this->xmlWriter->startElement('html');
                break;

            case 'XML':
                $this->xmlWriter->startDocument('1.0', 'UTF-8');
                break;

            case 'XHTML_1_1':
                $this->xmlWriter->startDTD(
                    'html',
                    '-//W3C//DTD XHTML 1.1//EN',
                    'http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd'
                );
                $this->xmlWriter->endDTD();

                $this->xmlWriter->startElementNS(null, 'html', 'http://www.w3.org/1999/xhtml');
                $this->xmlWriter->writeAttribute('lang', $this->language);
                $this->xmlWriter->writeAttribute('xml:lang', $this->language);
                break;

            default:
                throw new DomainException('Unknown docType');
        }
    }
}
// EOF
