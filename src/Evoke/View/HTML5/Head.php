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
    /**
     * Custom elements to be added to the head of the form:
     * `[tag, attribs, children]`
     * @var mixed[]
     */
    protected $customElements;

    /**
     * Array of links, with each link as an array of link attributes.
     * @var string[][]
     */
    protected $links;

    /**
     * Array of Meta elements with the key as the name and the value as the
     * content.
     * @var string[]
     */
    protected $metas;

    /**
     * Title
     * @var string
     */
    protected $title;

    /**
     * Construct a Head object.
     *
     * @param string[][] $links
     * Array of Links, with each link as an array of link attributes.
     * @param string[]   $metas
     * Array of meta elements with the key as the name and the value as the
     * content.
     * @param string     $title
     * @param mixed[]    $customElements Custom elements to be added to head.
     */
    public function __construct(Array        $links,
                                Array        $metas,
                                /* String */ $title,
                                Array        $customElements = [])
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
        $headElements = [['title', [], $this->title]];

        foreach ($this->metas as $name => $content)
        {
            $headElements[] =
                ['meta', ['name' => $name, 'content' => $content]];
        }

        foreach ($this->links as $linkAttributes)
        {
            $headElements[] = ['link', $linkAttributes];
        }

        foreach ($this->customElements as $customElement)
        {
            $headElements[] = $customElement;
        }

        return ['head', [], $headElements];
    }
}
// EOF
