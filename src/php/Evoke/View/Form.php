<?php
/**
 * Form View
 *
 * @package View
 */
namespace Evoke\View;

use LogicException;

/**
 * Form View
 *
 * @author Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license MIT
 * @package View
 */
class Form extends View
{
	private
		/**
		 * Whether to add elements to the row or form.
		 * @var bool
		 */
		$addToRow = false,

		/**
		 * Children of the form.
		 * @var mixed[]
		 */
		$children = array(),
		
		/**
		 * The elements in the current row.
		 * @var mixed[]
		 */
		$rowElements = array();
	
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
		if ($this->addToRow)
		{
			$this->rowElements[] = $element;
		}
		else
		{
			$this->children[] = $element;
		}
	}

	/**
	 * Add an input to the form.
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
	 * Add a hidden input to the form.
	 *
	 * @param string The name for the input.
	 * @param mixed  The value for the hidden input.
	 */
	public function addHidden($name, $value)
	{
		$this->add(array('input', array('type'  => 'hidden',
		                                'value' => $value)));
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
	public function addTextInput($name,
	                             $value,
	                             $length       = 30,
	                             $otherAttribs = array())
	{
		$attribs = array_merge($otherAttribs,
		                       array('length' => $length,
		                             'name'   => $name,
		                             'type'   => 'text',
		                             'value'  => $value));
		                             
		$this->add(array('input',
		                 array_merge($otherAttribs,
		                             array('length' => $length,
		                                   'name'   => $name,
		                                   'type'   => 'text',
		                                   'value'  => $value))));
	}

	/**
	 * Add a text area.
	 *
	 * @param string The name of the text area.
	 * @param string The initial text.
	 * @param int    The number of rows.
	 * @param int    The number of columns.
	 */
	public function addTextArea($name,
	                            $value,
	                            $rows = 10,
	                            $cols = 50)
	{
		$this->add(array('textarea',
		                 array('name' => $name,
		                       'rows' => $rows,
		                       'cols' => $cols),
		                 $value));
	}
	
	/**
	 * Finish a row in the form, adding it to the form elements.
	 */
	public function finishRow()
	{
		$this->children[] = array('div',
		                          array('class' => 'Row'),
		                          $this->rowElements);
		$this->addToRow = false;
		$this->rowElements = array();
	}
	
	/**
	 * Get the view of the form.
	 *
	 * @return mixed[] The view data.
	 */
	public function get()
	{
		$attribs = isset($this->params['Attribs']) ?
			$this->params['Attribs'] :
			array();

		if ($this->addToRow)
		{
			trigger_error('Started row has not been finished before the ' .
			              '\'get\' of the form.  Finishing the row now and ' .
			              'continuing', E_USER_WARNING);
			$this->finishRow();
		}
		
		return array('form', $attribs, $this->children);
	}

	/**
	 * Start a row (rows cannot be nested).
	 */
	public function startRow()
	{
		if ($this->addToRow)
		{
			throw new LogicException('Row already started, cannot nest rows.');
		}
		
		$this->addToRow = true;
		$this->rowElements = array();
	}
}
// EOF
