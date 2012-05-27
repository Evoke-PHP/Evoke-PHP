<?php
namespace Evoke\Element\Form\Input;

class Select extends \Evoke\Element
{
	/** @property appendData
	 *  @array Data to be appended to the options available for selection.
	 */
	protected $appendData;

	/** @property optionAttribs
	 *  @array Attributes for each select option.
	 */
	protected $optionAttribs;

	/** @property prependData
	 *  @array Data to be prepended to the options available for selection.
	 */
	protected $prependData;

	/** @property $textField
	 *  @string The field to use from the data for the option text.
	 */
	protected $textField;

	/** @property $valueField
	 *  @string The field to use for the value of the options.
	 */
	protected $valueField;

	/** Construct a Select object.
	 *  @param textField     @string Field from the data for the option text.
	 *  @param valueField    @string Field from the data for the option value.
	 *  @param attribs       @array  Attributes for the select element.
	 *  @param appendData    @array  Appended data for adding options.
	 *  @param optionAttribs @array  Attributes for the option elements.
	 *  @param prependData   @array  Prepended data for adding options.
	 */
	public function __construct(/* String */ $textField,
	                            /* String */ $valueField    = 'ID',
	                            Array        $attribs       = array(),
	                            Array        $appendData    = array(),
	                            Array        $optionAttribs = array(),
	                            Array        $prependData   = array())
	{
		if (!is_string($textField))
		{
			throw new \InvalidArgumentException(
				__METHOD__ . ' requires textField as string');
		}

		if (!is_string($valueField))
		{
			throw new \InvalidArgumentException(__METHOD__ . ' requires valueField as string');
		}

		parent::__construct($attribs);
		
		$this->appendData    = $appendData;
		$this->optionAttribs = $optionAttribs;
		$this->prependData   = $prependData;
		$this->textField     = $textField;
		$this->valueField    = $valueField;
	}
      
	/******************/
	/* Public Methods */
	/******************/

	/** Set the select element.
	 *  @param data @array The select data in the form:
	 *  @code
	 *  array('Records'  => \array records, // Records for the select
	 *        'Selected' => \scalar value); // The value that is selected.
	 *  @endcode
	 */    
	public function set(Array $data)
	{
		$data += array('Records'  => NULL,
		               'Selected' => NULL);

		if (!is_array($data['Records']))
		{
			throw new \InvalidArgumentException(
				__METHOD__ . ' requires Records as array');
		}

		$fullData = array_merge($this->prependData,
		                        $data['Records'],
		                        $this->appendData);

		if (empty($fullData))
		{
			throw new \RuntimeException(
				__METHOD__ . ' cannot set select element without having options ' .
				'to select from (The XHTML would be invalid).');
		}

		$optionElements = array();

		foreach ($fullData as $key => $record)
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

			$value = $record[$this->valueField];
			$optionAttribs = array_merge($this->optionAttribs,
			                             array('value' => $value));
	 
			if (isset($data['Selected']) && $value == $data['Selected'])
			{
				$optionAttribs['selected'] = 'selected';
			}
	 
			$optionElements[] =
				array('option', $optionAttribs, $record[$this->textField]);
		}

		return parent::set(array('select', array(), $optionElements));
	}
}
// EOF