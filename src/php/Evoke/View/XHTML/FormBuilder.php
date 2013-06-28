<?php
/**
 * Form Builder View
 *
 * @package View\XHTML
 */
namespace Evoke\View\XHTML;

use LogicException;

/**
 * Form Builder View
 *
 * @author    Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license   MIT
 * @package   View\XHTML
 */
class FormBuilder implements FormBuilderIface
{
	/**
	 * Children of the form.
	 * @var mixed[]
	 */
	protected $children = array();

	/**
	 * Construct a buildable XHTML form.
	 *
	 * @param string[]  Attribs.
	 */
	public function __construct(Array $attribs = array('action' => '',
	                                                   'method' => 'POST'))
	{
		$this->params['Attribs'] = $attribs;
		$this->params['Tag']     = 'form';
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
    public function addFile($name, Array $otherAttribs = array())
    {
        $this->add(
            array('input',
                  array('type' => 'file',
                        'name' => $name) +
                  $otherAttribs));
    }
    
	/**
	 * Add a hidden input to the form.
	 *
	 * @param string The name for the input.
	 * @param mixed  The value for the hidden input.
	 */
	public function addHidden($name, $value)
	{
		$this->add(array('input', array('type'  => 'hidden',
		                                'name'  => $name,
		                                'value' => $value)));
	}
	
	/**
	 * Add an input to the form.
	 *
	 * @param mixed[] Attributes for the input.
	 * @param mixed   Value for the input.
	 */
	public function addInput(Array $attribs, $value = NULL)
	{
		$element = array('input', $attribs);

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
		$this->add(array('label', array('for' => $for), $text));
	}

	/**
	 * Add a submit button to the form.
	 *
	 * @param string Name of the submit button.
	 * @param string Value for the button text.
	 */
	public function addSubmit($name, $value)
	{
		$this->add(array('input', array('type'  => 'submit',
		                                'name'  => $name,
		                                'value' => $value)));
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
                            $otherAttribs = array())
	{
		$this->add(array('input',
		                 $otherAttribs + array('length' => $length,
		                                       'name'   => $name,
		                                       'type'   => 'text',
		                                       'value'  => $value)));
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
	                            Array        $otherAttribs = array())
	{
		$this->add(array('textarea',
		                 $otherAttribs + array('name' => $name,
		                                       'rows' => $rows,
		                                       'cols' => $cols),
		                 $value));
	}
	
	/**
	 * Get the view of the form.
	 *
	 * @return mixed[] The view data.
	 */
	public function get()
	{
		return array('form', $this->params['Attribs'], $this->children);
	}

	/**
	 * Set the action of the form.
	 *
	 * @param string Action.
	 */
	public function setAction(/* String */ $action)
	{
		$this->params['Attribs']['action'] = $action;
	}
	
	/**
	 * Set the method of the form.
	 *
	 * @param string Method.
	 */
	public function setMethod(/* String */ $method)
	{
		$this->params['Attribs']['method'] = $method;
	}
}
// EOF
