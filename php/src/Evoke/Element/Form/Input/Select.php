<?php
namespace Evoke\Element\Input;

class Select extends \Evoke\Element
{
	public function __construct(Array $setup)
	{
		$setup += array('Append_Data'    => array(),
		                'Attribs'        => array(),
		                'Option_Attribs' => array(),
		                'Prepend_Data'   => array(),
		                'Text_Field'     => NULL,
		                'Value_Field'    => 'ID');

		parent::__construct($setup);

		if (!is_string($this->setup['Text_Field']))
		{
			throw new \InvalidArgumentException(
				__METHOD__ . ' requires Text_Field as string');
		}

		if (!is_string($this->setup['Value_Field']))
		{
			throw new \InvalidArgumentException(
				__METHOD__ . ' requires Value_Field as string');
		}
	}
      
	/******************/
	/* Public Methods */
	/******************/

	/** Set the select element.
	 *  @param select \array The select data in the form:
	 *  \verbatim
	 array('Data'     => \array records, // Records for the select
	 'Selected' => \scalar value); // The value that is selected.
	 \endverbatim
	*/    
	public function set(Array $select)
	{
		$select += array('Data'     => array(),
		                 'Selected' => NULL);
		$optionElements = array();

		$fullData = array_merge($this->setup['Prepend_Data'],
		                        $select['Data'],
		                        $this->setup['Append_Data']);

		foreach ($fullData as $key => $record)
		{
			if (!isset($record[$this->setup['Text_Field']]) ||
			    !isset($record[$this->setup['Value_Field']]))
			{
				throw new \InvalidArgumentException(
					__METHOD__ . ' Record: ' . var_export($record, true) .
					' at key: ' . $key . ' does not contain the required fields ' .
					'Text_Field: ' . $this->setup['Text_Field'] .
					' and Value_Field: ' . $this->setup['Value_Field']);
			}

			$value = $record[$this->setup['Value_Field']];
			$optionAttribs = array_merge($this->setup['Option_Attribs'],
			                             array('value' => $value));
	 
			if (isset($select['Selected']) && $value == $select['Selected'])
			{
				$optionAttribs['selected'] = 'selected';
			}
	 
			$optionElements[] =
				array('option',
				      $optionAttribs,
				      array('Text' => $record[$this->setup['Text_Field']]));
		}

		return parent::set(array('select',
		                         $this->setup['Attribs'],
		                         array('Children' => $optionElements)));
	}
}
// EOF