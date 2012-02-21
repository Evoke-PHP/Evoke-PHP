<?php
namespace Evoke\Element\Form;

class Checkboxes extends \Evoke\Element\Base
{ 
	public function __construct(Array $setup)
	{
		$setup += array('Empty_Text'       => NULL,
		                'Fieldset_Attribs' => array('class' => 'Checkbox_Group'),
		                'Prefix'           => '',
		                'Text_Field'       => NULL,
		                'Value_Field'      => 'ID');

		parent::__construct($setup);

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
	}

	/** Set the checkboxes element which is a fieldset of checkbox inputs.
	 *  @param data \array The checkbox data of the form:
	 *  \verbatim
	 array('Checkboxes' => array()  // Array of records.
	 'Selected'   => array()) // Array of values that are checked.
	 \endverbatim
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
				      array('Children' => array(
					            array(
						            'div',
						            array('class' => 'No_Elements'),
						            array('Text' => $this->emptyText))))));
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
	 
			$id = $this->setup['Prefix'] . $record[$this->valueField];
			$isSelected = array();
	 
			if (in_array($record[$this->valueField], $data['Selected']))
			{
				$isSelected = array('checked' => 'checked');
			}
	 
			$checkboxElems[] = array(
				'div',
				array('class' => 'Encasing'),
				array('Children' => array(
					      array(
						      'label',
						      array('for' => $id),
						      array('Text' => $record[$this->textField])),
					      array(
						      'input',
						      array_merge(array('type' => 'checkbox',
						                        'id'   => $id,
						                        'name' => $id),
						                  $isSelected)))));
		}
      
		// Set the fieldset to make the category selections from.
		return parent::set(array('fieldset',
		                         $this->fieldsetAttribs,
		                         array('Children' => $checkboxElems)));
	}
}
// EOF