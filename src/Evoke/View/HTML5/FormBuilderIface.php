<?php
/**
 * Form Builder Interface
 *
 * @package View\HTML5
 */
namespace Evoke\View\HTML5;

use Evoke\View\ViewIface;

/**
 * Form Builder Interface
 *
 * @author    Paul Young <evoke@youngish.org>
 * @copyright Copyright (c) 2014 Paul Young
 * @license   MIT
 * @package   View\HTML5
 */
interface FormBuilderIface extends ViewIface
{
    /******************/
    /* Public Methods */
    /******************/

    /**
     * Append an element to the form.
     *
     * @param mixed[] $element The element to add to the form.
     */
    public function add(Array $element);

    /**
     * Add a file input to the form.
     *
     * @param string   $name         The name for the input.
     * @param string[] $otherAttribs Any other attributes.
     */
    public function addFile($name, Array $otherAttribs = []);

    /**
     * Add a hidden input to the form.
     *
     * @param string $name  The name for the input.
     * @param mixed  $value The value for the hidden input.
     */
    public function addHidden($name, $value);

    /**
     * Add an input to the form.
     *
     * @param mixed[] $attribs Attributes for the input.
     * @param mixed   $value   Value for the input.
     */
    public function addInput(Array $attribs, $value = null);

    /**
     * Add a label to the form.
     *
     * @param string $for  The id for the input that this label is for.
     * @param string $text The text for the label.
     */
    public function addLabel($for, $text);

    /**
     * Add a submit button to the form.
     *
     * @param string $name  Name of the submit button.
     * @param string $value Value for the button text.
     */
    public function addSubmit($name, $value);

    /**
     * Add a text input.
     *
     * @param string  $name         The name of the input.
     * @param string  $value        The initial text.
     * @param int     $length       The length of the text.
     * @param mixed[] $otherAttribs Other attributes for the input.
     */
    public function addText(
        $name,
        $value,
        $length = 30,
        $otherAttribs = []
    );

    /**
     * Add a text area.
     *
     * @param string   $name         Name of the text area.
     * @param string   $value        Initial text.
     * @param int      $rows         Number of rows.
     * @param int      $cols         Number of columns.
     * @param string[] $otherAttribs Other attributes.
     */
    public function addTextArea(
        $name,
        $value,
        $rows = 10,
        $cols = 50,
        Array $otherAttribs = []
    );

    /**
     * Set the action of the form.
     *
     * @param string $action
     */
    public function setAction($action);

    /**
     * Set the method of the form.
     *
     * @param string $method
     */
    public function setMethod($method);
}
// EOF
