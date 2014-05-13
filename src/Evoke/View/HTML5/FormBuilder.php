<?php
/**
 * Form Builder View
 *
 * @package View\HTML5
 */
namespace Evoke\View\HTML5;

use LogicException;

/**
 * Form Builder View
 *
 * @author    Paul Young <evoke@youngish.org>
 * @copyright Copyright (c) 2014 Paul Young
 * @license   MIT
 * @package   View\HTML5
 */
class FormBuilder implements FormBuilderIface
{
    /**
     * Attributes for the form.
     * @var mixed[]
     */
    protected $attribs;

    /**
     * Children of the form.
     * @var mixed[]
     */
    protected $children = [];

    /**
     * Construct a buildable HTML5 form.
     *
     * @param string[]  Attribs.
     */
    public function __construct(Array $attribs = ['action' => '',
                                                  'method' => 'POST'])
    {
        $this->attribs = $attribs;
    }

    /******************/
    /* Public Methods */
    /******************/

    /**
     * Append an element to the form.
     *
     * @param mixed[] The element to add to the form.
     */
    public function add(Array $element)
    {
        $this->children[] = $element;
    }

    /**
     * Add a file input to the form.
     *
     * @param string   The name for the input.
     * @param string[] Any other attributes.
     */
    public function addFile($name, Array $otherAttribs = [])
    {
        $this->add(['input',
                    ['name' => $name, 'type' => 'file'] + $otherAttribs]);
    }

    /**
     * Add a hidden input to the form.
     *
     * @param string The name for the input.
     * @param mixed  The value for the hidden input.
     */
    public function addHidden($name, $value)
    {
        $this->add(['input',
                    ['name'  => $name,
                     'type'  => 'hidden',
                     'value' => $value]]);
    }

    /**
     * Add an input to the form.
     *
     * @param mixed[] Attributes for the input.
     * @param mixed   Value for the input.
     */
    public function addInput(Array $attribs, $value = NULL)
    {
        $element = ['input', $attribs];

        if (isset($value))
        {
            $element[] = $value;
        }

        $this->children[] = $element;
    }

    /**
     * Add a label to the form.
     *
     * @param string The id for the input that this label is for.
     * @param string The text for the label.
     */
    public function addLabel($for, $text)
    {
        $this->add(['label', ['for' => $for], $text]);
    }

    /**
     * Add a submit button to the form.
     *
     * @param string Name of the submit button.
     * @param string Value for the button text.
     */
    public function addSubmit($name, $value)
    {
        $this->add(['input',
                    ['name'  => $name,
                     'type'  => 'submit',
                     'value' => $value]]);
    }

    /**
     * Add a text input.
     *
     * @param string  The name of the input.
     * @param string  The initial text.
     * @param int     The length of the text.
     * @param mixed[] Other attributes for the input.
     */
    public function addText($name,
                            $value,
                            $length       = 30,
                            $otherAttribs = [])
    {
        $this->add(['input',
                    $otherAttribs + ['length' => $length,
                                     'name'   => $name,
                                     'type'   => 'text',
                                     'value'  => $value]]);
    }

    /**
     * Add a text area.
     *
     * @param string   Name of the text area.
     * @param string   Initial text.
     * @param int      Number of rows.
     * @param int      Number of columns.
     * @param string[] Other attributes.
     */
    public function addTextArea(/* String */ $name,
                                /* String */ $value,
                                /* Int    */ $rows         = 10,
                                /* Int    */ $cols         = 50,
                                Array        $otherAttribs = [])
    {
        $this->add(['textarea',
                    $otherAttribs + ['cols' => $cols,
                                     'name' => $name,
                                     'rows' => $rows],
                    $value]);
    }

    /**
     * Get the view of the form.
     *
     * @return mixed[] The view data.
     */
    public function get()
    {
        return ['form', $this->attribs, $this->children];
    }

    /**
     * Set the action of the form.
     *
     * @param string Action.
     */
    public function setAction(/* String */ $action)
    {
        $this->attribs['action'] = $action;
    }

    /**
     * Set the method of the form.
     *
     * @param string Method.
     */
    public function setMethod(/* String */ $method)
    {
        $this->attribs['method'] = $method;
    }
}
// EOF
