<?php
declare(strict_types = 1);
/**
 * XML Writer
 *
 * @package Writer
 */
namespace Evoke\Writer;

use InvalidArgumentException;
use LogicException;
use Throwable;
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
     * XML Writer object.
     * @var XMLWriter
     */
    protected $xmlWriter;

    /**
     * Create an XML Writer.
     *
     * @param XMLWriter $xmlWriter    XMLWriter object.
     * @param bool      $indent       Whether the XML produced should be indented.
     * @param string    $indentString The string that should be used to indent the XML.
     */
    public function __construct(XMLWriter $xmlWriter, bool $indent = true, string $indentString = '    ')
    {
        $this->indent    = $indent;
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
    public function __toString() : string
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
     * @param mixed[] $xml Array for the xml to be written of the form: `[$tag, $attributes, $children]`
     * @throws InvalidArgumentException for bad xml data.
     */
    public function write($xml)
    {
        if (is_array($xml) && count($xml) <= 3) {
            try {
                $this->writeXMLElement(...$xml);
            } catch (Throwable $thrown) {
                throw new LogicException('Failure writing: ' . var_export($xml, true), 0, $thrown);
            }
        } else {
            throw new InvalidArgumentException('Bad root element: ' . var_export($xml, true));
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
     * Write the start of the document.
     */
    public function writeStart()
    {
        $this->xmlWriter->startDocument('1.0', 'UTF-8');
    }

    /*********************/
    /* Protected Methods */
    /*********************/

    /**
     * Write XML children and their descendants.
     *
     * @param array $children
     */
    protected function writeXMLChildren(array $children)
    {
        foreach ($children as $child) {
            if (is_string($child)) {
                $this->xmlWriter->text($child);
            } elseif (is_array($child) && count($child) <= 3) {
                $this->writeXMLElement(...$child);
            } else {
                throw new InvalidArgumentException('Bad child: ' . var_export($child, true));
            }
        }
    }

    /**
     * Write an element and all its children
     *
     * @param string            $tag
     * @param array             $attribs
     * @param array|string|null $children Children can be specified as a single text node or a list of child nodes.
     */
    protected function writeXMLElement(string $tag, array $attribs = [], $children = null)
    {
        $this->xmlWriter->startElement($tag);

        foreach ($attribs as $attrib => $value) {
            $this->xmlWriter->writeAttribute($attrib, $value);
        }

        if (isset($children)) {
            if (is_string($children)) {
                $this->xmlWriter->text($children);
            } else {
                $this->writeXMLChildren($children);
            }
        }

        $this->xmlWriter->endElement();
    }
}
// EOF
