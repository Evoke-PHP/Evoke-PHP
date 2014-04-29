<?php
/**
 * HTML5 Head View
 *
 * @package View\HTML5
 */
namespace Evoke\View\HTML5;

use Evoke\View\ViewIface;

/**
 * HTML5 Head View
 *
 * @author    Paul Young <evoke@youngish.org>
 * @copyright Copyright (c) 2014 Paul Young
 * @license   MIT
 * @package   View\HTML5
 */
class Head implements ViewIface
{
    protected
        /**
         * Custom elements to be added to the head of the form:
         * `array(tag, attribs, children)`
         * @var mixed[]
         */
        $customElements,
        
        /**
         * Array of links, with each link as an array of link attributes.
         * @var string[][]
         */
        $links,

        /**
         * Array of Meta elements with the key as the name and the value as the
         * content.
         * @var string[]
         */
        $metas,

        /**
         * Title
         * @var string
         */
        $title;
        
	/**
	 * Construct a Head object.
	 *
	 * @param string[][] Array of Links, with each link as an array of link
     *                   attributes.
	 * @param string[]   Array of meta elements with the key as the name and the
     *                   value as the content.
	 * @param string     Title.
     * @param mixed[]    Custom elements to be added to head.
	 */
	public function __construct(Array        $links,
                                Array        $metas,
                                /* String */ $title,
                                Array        $customElements = array())
	{
        $this->customElements = $customElements;
        $this->links          = $links;
        $this->metas          = $metas;
		$this->title          = $title;
	}
	
	/******************/
	/* Public Methods */
	/******************/

	/**
	 * Get the output from the view.
	 *
	 * @return mixed[] The output from the view.
	 */
	public function get()
	{       
		$headElements = array(array('title', array(), $this->title));

        foreach ($this->metas as $name => $content)
        {
            $headElements[] =
                array('meta', array('name' => $name, 'content' => $content));
        }

        foreach ($this->links as $linkAttributes)
        {
            $headElements[] = array('link', $linkAttributes);
        }

        foreach ($this->customElements as $customElement)
        {
            $headElements[] = $customElement;
        }
        
		return array('head', array(), $headElements);
	}
}
// EOF
