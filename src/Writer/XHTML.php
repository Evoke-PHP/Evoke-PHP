<?php
/**
 * XHTML
 *
 * @package   Evoke\Writer
 */
namespace Evoke\Writer;

use InvalidArgumentException;
use LogicException;
use Throwable;
use XMLWriter;

/**
 * XHTML
 *
 * @author    Paul Young <evoke@youngish.org>
 * @copyright Copyright (c) 2015 Paul Young
 * @package   Evoke\Writer
 */
class XHTML extends XML
{
    /**
     * Language of XML being written.
     * @var string
     */
    protected $language;

    /**
     * Create an HTML5 Writer.
     *
     * @param XMLWriter $xmlWriter    XMLWriter object.
     * @param bool      $indent       Whether the XML produced should be indented.
     * @param string    $indentString The string that should be used to indent the XML.
     * @param string    $language     Language of XML being written.
     */
    public function __construct(
        XMLWriter $xmlWriter,
        bool      $indent = true,
        string    $indentString = '    ',
        string    $language = 'EN')
    {
        parent::__construct($xmlWriter, $indent, $indentString);

        $this->language  = $language;
    }

    /******************/
    /* Public Methods */
    /******************/

    /**
     * Write XHTML elements into the memory buffer.
     *
     * @param string|mixed[] $xhtml String or Array in the form: `[$tag, $attributes, $children]` to be written.
     * @throws InvalidArgumentException for bad xml data.
     */
    public function write($xhtml)
    {
        if (is_string($xhtml)) {
            $this->xmlWriter->text($xhtml);
        } elseif (is_array($xhtml) && 3 === count($xhtml)) {
            try {
                $this->writeXHTMLElement($xhtml[0], $xhtml[1], $xhtml[2]);
            } catch (Throwable $thrown) {
                throw new LogicException('Failure writing: ' . var_export($xhtml, true), 0, $thrown);
            }
        } else {
            throw new InvalidArgumentException('Bad root element: ' . var_export($xhtml, true));
        }
    }

    public function writeStart()
    {
        $this->xmlWriter->startDtd(
            'html',
            '-//W3C//DTD XHTML 1.1//EN',
            'http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd'
        );
        $this->xmlWriter->endDtd();

        $this->xmlWriter->startElementNS(null, 'html', 'http://www.w3.org/1999/xhtml');
        $this->xmlWriter->writeAttribute('lang', $this->language);
        $this->xmlWriter->writeAttribute('xml:lang', $this->language);
        $this->xmlWriter->writeAttribute('class', 'no-js');
    }

    /*********************/
    /* Protected Methods */
    /*********************/

    /**
     * Write an element and all its children
     *
     * @param string       $tag
     * @param array        $attribs
     * @param array|string $children
     */
    protected function writeXHTMLElement(string $tag, array $attribs, $children)
    {
        // Whether we are normally indenting and we see an element that should be inline.
        $specialInlineElement = ($this->indent && preg_match('(^(strong|em|pre|code)$)i', $tag));

        if ($specialInlineElement) {
            // Toggle the indent off.
            $this->xmlWriter->setIndent(false);
        }

        $this->xmlWriter->startElement($tag);

        foreach ($attribs as $attrib => $value) {
            $this->xmlWriter->writeAttribute($attrib, $value);
        }

        if (is_string($children)) {
            $this->xmlWriter->text($children);
        } elseif (is_array($children)) {
            foreach ($children as $child) {
                if (is_string($child)) {
                    $this->xmlWriter->text($child);
                } elseif (is_array($child) && 3 == count($child)) {
                    $this->writeXHTMLElement($child[0], $child[1], $child[2]);
                } else {
                    throw new InvalidArgumentException('Bad child: ' . var_export($child, true));
                }
            }
        } else {
            throw new InvalidArgumentException('Bad children: ' . var_export($children, true));
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
}
// EOF
