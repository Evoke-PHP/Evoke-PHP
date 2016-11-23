<?php
/**
 * HTML5 Text View
 *
 * @package View\HTML5
 */
namespace Evoke\View\HTML5;

use DOMDocument;
use DOMNode;
use Evoke\View\ViewIface;

/**
 * HTML5 Text View
 *
 * @author    Paul Young <evoke@youngish.org>
 * @copyright Copyright (c) 2015 Paul Young
 * @license   MIT
 * @package   View\HTML5
 */
class Text implements ViewIface
{
    /**
     * HTML5 string for parsing into the array based view.
     * @var string
     */
    protected $html5String;

    /******************/
    /* Public Methods */
    /******************/

    /**
     * Get the view of the HTML5 in a suitable manner for inclusion of an element (a single string, or an array of child
     * elements).
     *
     * @return string|mixed[][]
     */
    public function get()
    {
        $dom     = new \DOMDocument;
        $domNode = $dom->createDocumentFragment();
        $domNode->appendXML($this->html5String);

        return $this->convertDOMNodeForWriting($domNode);
    }

    /**
     * Set the string for the view.
     *
     * @param string $html5String The html string to parse for the view.
     */
    public function setHTML5($html5String)
    {
        $this->html5String = $html5String;
    }

    /*********************/
    /* Protected Methods */
    /*********************/

    /**
     * Convert a DOM Node to an array.
     *
     * @param DOMNode $node The DOM node to convert.
     * @return mixed[][]|string The DOM node converted read for writing.
     */
    protected function convertDOMNodeForWriting(DOMNode $node)
    {
        $attributes = $this->getAttributes($node);

        switch ($node->nodeType) {
            case XML_TEXT_NODE:
            case XML_CDATA_SECTION_NODE:
                return $node->nodeValue;

            case XML_DOCUMENT_FRAG_NODE:
            case XML_HTML_DOCUMENT_NODE:
                $data      = [];
                $childData =& $data;
                break;

            default:
                $data      = [$node->nodeName, $attributes, []];
                $childData =& $data[2];
        }

        if ($node->hasChildNodes()) {
            foreach ($node->childNodes as $child) {
                if ($child->nodeType === XML_COMMENT_NODE) {
                    continue;
                }

                $childElements = $this->convertDOMNodeForWriting($child);

                if (is_string($childElements) &&
                    $node->childNodes->length === 1
                ) {
                    $childData = $childElements;
                } else {
                    $childData[] = $childElements;
                }
            }
        }

        return $data;
    }

    /**
     * Get the attributes for the DOM node.
     *
     * @param DOMNode $node
     * @return mixed[] The attributes for the node.
     */
    protected function getAttributes(DOMNode $node)
    {
        $attributes = [];

        if ($node->hasAttributes()) {
            foreach ($node->attributes as $attrib => $attribNode) {
                $attributes[$attrib] = $attribNode->nodeValue;
            }
        }

        return $attributes;
    }
}
// EOF
