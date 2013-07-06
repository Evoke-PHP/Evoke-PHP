<?php
/**
 * Select Input View
 *
 * @package View\XHTML\Input
 */
namespace Evoke\View\XHTML\Input;

use Evoke\View\Data,
	LogicException;

/**
 * Select Input View
 *
 * @author    Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license   MIT
 * @package   View\XHTML
 */
class Select extends Data
{
	/**
	 * Protected properties.
	 *
	 * @var string[]   $attribs       Attributes for the select element.
	 * @var string     $fieldText     Field in the data for the option text.
	 * @var string     $fieldValue    Field in the data for the option value.
	 * @var string[]   $optionAttribs Attributes for each select option.
	 * @var mixed|null $selectedValue Value that is selected or NULL for none.
	 */
	protected $attribs, $fieldText, $fieldValue, $optionAttribs, $selectedValue;

	/**
	 * Construct a Select view.
	 *
	 * @param string   Field from the data for the option text.
	 * @param string   Field from the data for the option value.
	 * @param string[] Attributes for the select element.
	 * @param string[] Attributes for the option elements.
	 */
	public function __construct(/* String */ $fieldText,
	                            /* String */ $fieldValue    = 'ID',
	                            Array        $attribs       = array(),
	                            Array        $optionAttribs = array())
	{
		$this->attribs       = $attribs;
		$this->fieldText     = $fieldText;
		$this->fieldValue    = $fieldValue;
		$this->optionAttribs = $optionAttribs;
	}
      
	/******************/
	/* Public Methods */
	/******************/

	/**
	 * Get the select element.
	 *
	 * @return mixed[] The select element.
	 */    
	public function get()
	{
		if (empty($this->data))
		{
			throw new LogicException(
				'Select element must have options to be valid XHTML.');
		}

		if (!isset($this->data[$this->fieldText],
		           $this->data[$this->fieldValue]))
		{
			throw new LogicException(
				'needs Data with TextField: ' . $this->fieldText .
				' and ValueField: ' . $this->fieldValue);
		}

		$optionElements = array();

		foreach ($this->data as $record)
		{
			$value = $record[$this->fieldValue];
			$optionAttribs = array_merge($this->optionAttribs,
			                             array('value' => $value));
	 
			if (isset($this->selectedValue) &&
			    $value == $this->selectedValue)
			{
				$optionAttribs['selected'] = 'selected';
			}
	 
			$optionElements[] =
				array('option', $optionAttribs, $record[$this->fieldText]);
		}

		return array('select', $this->attribs, $optionElements);
	}

	/**
	 * Set the value that has been selected from the options.
	 *
	 * @param mixed The selected value.
	 */
	public function setSelected($selectedValue)
	{
		$this->selectedValue = $selectedValue;
	}
}
// EOF