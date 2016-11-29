<?php
declare(strict_types = 1);
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
 * @copyright Copyright (c) 2015 Paul Young
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
    public function add(array $element);

    /**
     * Add a file input to the form.
     *
     * @param string   $name         The name for the input.
     * @param string[] $otherAttribs Any other attributes.
     */
    public function addFile(string $name, array $otherAttribs = []);

    /**
     * Add a hidden input to the form.
     *
     * @param string $name  The name for the input.
     * @param mixed  $value The value for the hidden input.
     */
    public function addHidden(string $name, $value);

    /**
     * Add an input to the form.
     *
     * @param mixed[] $attribs Attributes for the input.
     * @param mixed   $value   Value for the input.
     */
    public function addInput(array $attribs, $value = null);

    /**
     * Add a label to the form.
     *
     * @param string $for  The id for the input that this label is for.
     * @param string $text The text for the label.
     */
    public function addLabel(string $for, string $text);

    /**
     * Add a radio button to the form.
     *
     * @param string $name  Name of the radio button.
     * @param string $value Value of the radio.
     */
    public function addRadio(string $name, string $value);

    /**
     * Add a submit button to the form.
     *
     * @param string $name  Name of the submit button.
     * @param string $value Value for the button text.
     */
    public function addSubmit(string $name, string $value);

    /**
     * Add a text input.
     *
     * @param string  $name         The name of the input.
     * @param string  $value        The initial text.
     * @param int     $length       The length of the text.
     * @param mixed[] $otherAttribs Other attributes for the input.
     */
    public function addText(string $name, string $value, int $length = 30, array $otherAttribs = []);

    /**
     * Add a text area.
     *
     * @param string   $name         Name of the text area.
     * @param string   $value        Initial text.
     * @param int      $rows         Number of rows.
     * @param int      $cols         Number of columns.
     * @param string[] $otherAttribs Other attributes.
     */
    public function addTextArea(string $name, string $value, int $rows = 10, int $cols = 50, array $otherAttribs = []);

    /**
     * Set the action of the form.
     *
     * @param string $action
     */
    public function setAction(string $action);

    /**
     * Set the method of the form.
     *
     * @param string $method
     */
    public function setMethod(string $method);
}
// EOF
