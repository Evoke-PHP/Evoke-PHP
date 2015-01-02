<?php
/**
 * Message Box View
 *
 * @package View\HTML5
 */
namespace Evoke\View\HTML5;

use Evoke\View\ViewIface;

/**
 * Message Box View
 *
 * @author    Paul Young <evoke@youngish.org>
 * @copyright Copyright (c) 2014 Paul Young
 * @license   MIT
 * @package   View\HTML5
 */
class MessageBox implements ViewIface
{
    /**
     * Attributes.
     * @var mixed[]
     */
    protected $attribs;

    /**
     * Content Elements.
     * @var mixed[]
     */
    protected $contentElements;

    /**
     * Title.
     * @var string
     */
    protected $title;

    /**
     * Construct a Box object.
     *
     * @param mixed[] $attribs Message Box attributes.
     */
    public function __construct(
        Array $attribs = ['class' => 'Message_Box Info'])
    {
        $this->attribs         = $attribs;
        $this->contentElements = [];
        $this->title           = 'Message Box';
    }

    /******************/
    /* Public Methods */
    /******************/

    /**
     * Add a content element to the message box.
     *
     * @param mixed $element Message box element.
     */
    public function addContent($element)
    {
        $this->contentElements[] = $element;
    }

    /**
     * Get the output for the view.
     *
     * @return mixed[] Output of the view.
     */
    public function get()
    {
        return ['div',
                $this->attribs,
                [['div', ['class' => 'Title'], $this->title],
                 ['div', ['class' => 'Content'], $this->contentElements]]];
    }

    /**
     * Set the title for the message box.
     *
     * @param string $title Title of the message box.
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }
}
// EOF
