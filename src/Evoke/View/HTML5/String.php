<?php
/**
 * HTML5 String View
 *
 * @package View\HTML5
 */
namespace Evoke\View\HTML5;

use DOMDocument,
    DOMNode,
    Evoke\View\ViewIface;

/**
 * HTML5 String View
 *
 * @author    Paul Young <evoke@youngish.org>
 * @copyright Copyright (c) 2014 Paul Young
 * @license   MIT
 * @package   View\HTML5
 */
class String implements ViewIface
{
    protected
        /**
         * Attribs for the root element that holds the parsed string.
         * @var mixed[]
         */
        $attribs,

        /**
         * DOM Document object used to parse the string.
         * @var DOMDocument
         */
        $dom,
        
        /**
         * HTML5 string for parsing into the array based view.
         * @var string
         */
        $html5String,
        
        /**
         * Tag for the root element that holds the parsed string.
         * @var string
         */
        $tag;


    /**
     * Construct a String object.
     *
     * @param DOMDocument The DOMDocument object used to parse the string.
     * @param string  Tag.
     * @param mixed[] Attribs.
     */
    public function __construct(DOMDocument  $dom,
                                /* string */ $tag = 'div',
                                Array        $attribs = [])
    {
        $this->attribs = $attribs;
        $this->dom     = $dom;
        $this->tag     = $tag;
    }
    
    /******************/
    /* Public Methods */
    /******************/

    public function get()
    {
        $this->dom->loadHTML(
            $this->html5String,
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD | LIBXML_PEDANTIC);
        
        return $this->convertDOMNodeToArray($this->dom);
    }

    /**
     * Set the attributes for the root element.
     *
     * @var mixed[] The attributes.
     */
    public function setAttribs(Array $attribs)
    {
        $this->attribs = $attribs;
    }
    
    /**
     * Set the string for the view.
     *
     * @var string The html string to parse for the view.
     */
    public function setHTML5(/* String */ $html5String)
    {
        $this->html5String = $html5String;
    }

    /**
     * Set the tag used for the root element.
     *
     * @var string The tag.
     */
    public function setTag(/* String */ $tag)
    {
        $this->tag = $tag;
    }
    
    /*********************/
    /* Protected Methods */
    /*********************/

    protected function convertDOMNodeToArray(DOMNode $node)
    {
        $attributes = [];
        
        if ($node->hasAttributes())
        {
            foreach ($node->attributes as $attrib => $attribNode)
            {
                $attributes[$attrib] = $attribNode->nodeValue;
            }
        }
        
        $type = $node->nodeType;
        
        switch ($type)
        {
        case XML_TEXT_NODE:
        case XML_CDATA_SECTION_NODE:
            return $node->nodeValue;
            
        case XML_HTML_DOCUMENT_NODE:
            $data = [$this->tag, $this->attribs, []];
            break;
            
        default:
            $data = [$node->nodeName, $attributes, []];
        }
        
        $childData =& $data[2];
        
        if ($node->hasChildNodes())
        {
            foreach ($node->childNodes as $child)
            {
                $childElements = $this->convertDOMNodeToArray($child);

                if (is_string($childElements) &&
                    $node->childNodes->length === 1)
                {
                    $childData = $childElements;
                }
                else
                {
                    $childData[] = $childElements;
                }
            }
        }
        
        return $data;
    }
}
// EOF