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
class Form extends Element
{
	/**
	 * Whether to add elements to the row or form.
	 * @var bool
	 */
	private $addToRow = false;
	
	/**
	 * The elements in the current row.
	 * @var mixed[]
	 */
	private $rowElements = array();

	/**
	 * Construct a Form object.
	 *
	 * @param mixed[] Attribs.
	 */
	public function __construct(Array $attribs)
	{
		$this->attribs = $attribs;

		parent::__construct('form', $this->attribs);
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
	 * @param mixed[] Parameters for retrieving the view.
	 *
	 * @return mixed[] The view data.
	 */
	public function get(Array $params = array())
	{
		return array('form',
		             $this->attribs,
		             $this->children);
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
