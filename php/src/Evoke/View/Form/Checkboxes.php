<?php
namespace Evoke\View\XHTML\Form;

/**
 * Checkboxes
 *
 * @author Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license MIT
 * @package View
 */
class Checkboxes extends Form
{
	/**
	 *  The text \string if there are no checkboxes.
	 */
	protected $emptyText;

	/**
	 *  Prefix \string for the id attribute of the checkboxes.
	 */
	protected $prefix;

	/**
	 *  \string The field to use for the checkbox text.
	 */
	protected $textField;

	/**
	 *  \string The field that defines the value of the checkbox.
	 */
	protected $valueField;

	/**
	 * @todo Check if this class is obsolete.
	 */
	public function __construct(Array $setup)
	{
		/// @todo Fix to new View interface.
		throw new \RuntimeException('Fix to new view interface.');

		$setup += array('Attribs'     => array('class' => 'Checkbox_Group'),
		                'Empty_Text'  => NULL,
		                'Prefix'      => '',
		                'Text_Field'  => NULL,
		                'Value_Field' => 'ID');

		if (!is_string($this->emptyText))
		{
			throw new \InvalidArgumentException(
				__METHOD__ . ' requires Empty_Text as string');
		}

		if (!is_string($this->textField))
		{
			throw new \InvalidArgumentException(
				__METHOD__ . ' requires Text_Field as string');
		}

		if (!is_string($this->valueField))
		{
			throw new \InvalidArgumentException(
				__METHOD__ . ' requires Value_Field as string');
		}

		parent::__construct($setup);

		$this->emptyText  = $emptyText;
		$this->prefix     = $prefix;
		$this->textField  = $textField;
		$this->valueField = $valueField;
	}

	/** Set the checkboxes element which is a fieldset of checkbox inputs.
	 *  @param data \array The checkbox data of the form:
	 *  \verbatim
	 *  array('Checkboxes' => array()  // Array of records.
	 *        'Selected'   => array()) // Array of values that are checked.
	 *  \endverbatim
	 */
	public function set(Array $data)
	{
		$data += array('Checkboxes'    => NULL,
		               'Selected'      => NULL);
		            
		if (!is_array($data['Checkboxes']))
		{
			throw new \InvalidArgumentException(
				__METHOD__ . ' requires Checkboxes as array');
		}

		if (!is_array($data['Selected']))
		{
			throw new \InvalidArgumentException(
				__METHOD__ . ' requires Selected as array');
		}
      
		if (empty($data['Checkboxes']))
		{
			// Build an element to show that there are no checkboxes defined.
			return parent::set(
				array('div',
				      array('class' => 'Group_Container'),
				      array(array('div',
				                  array('class' => 'No_Elements'),
				                  $this->emptyText))));
		}

		$checkboxElems = array();
      
		foreach ($data['Checkboxes'] as $key => $record)
		{
			if (!isset($record[$this->textField]) ||
			    !isset($record[$this->valueField]))
			{
				throw new \InvalidArgumentException(
					__METHOD__ . ' Record: ' . var_export($record, true) .
					' at key: ' . $key . ' does not contain the required fields ' .
					'Text_Field: ' . $this->textField .
					' and Value_Field: ' . $this->valueField);
			}
	 
			$id = $this->prefix . $record[$this->valueField];
			$isSelected = array();
	 
			if (in_array($record[$this->valueField], $data['Selected']))
			{
				$isSelected = array('checked' => 'checked');
			}
	 
			$checkboxElems[] = array(
				'div',
				array('class' => 'Encasing'),
				array(array('label',
				            array('for' => $id),
				            $record[$this->textField]),
				      array('input',
				            array_merge(array('type' => 'checkbox',
				                              'id'   => $id,
				                              'name' => $id),
				                        $isSelected))));
		}
      
		// Set the fieldset to make the category selections from.
		return parent::set(array('fieldset',
		                         $this->fieldsetAttribs,
		                         $checkboxElems));
	}

	protected function buildFormElements()
	{
		/** @todo Implement this method.
		 */
		throw new \RuntimeException(__METHOD__ . ' not yet implemented.');
	}
}
// EOF