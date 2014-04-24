<?php
/**
 * Select Input View
 *
 * @package View\XHTML\Input
 */
namespace Evoke\View\XHTML\Input;

use InvalidArgumentException,
	LogicException;

/**
 * Select Input View
 *
 * @author    Paul Young <evoke@youngish.org>
 * @copyright Copyright (c) 2014 Paul Young
 * @license   MIT
 * @package   View\XHTML
 */
class Select
{
	/**
	 * Protected properties.
	 *
	 * @var string[]   $attribs       Attributes for the select element.
	 * @var string     $fieldText     Field in the data for the option text.
	 * @var string     $fieldValue    Field in the data for the option value.
	 * @var string[]   $optionAttribs Attributes for each select option.
	 * @var mixed[]    $options       Options for the select.
	 * @var mixed|null $selectedValue Value that is selected or NULL for none.
	 */
	protected $attribs, $fieldText, $fieldValue, $optionAttribs, $options,
		$selectedValue;

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
		if (empty($this->options))
		{
			throw new LogicException(
				'Select element must have options to be valid XHTML.');
		}

		$optionElements = array();

		foreach ($this->options as $record)
		{
			if (!isset($record[$this->fieldText],
			           $record[$this->fieldValue]))
			{
				throw new LogicException(
					'Option needs TextField: ' . $this->fieldText .
					' and ValueField: ' . $this->fieldValue);
			}
			
			$optionAttribs = array_merge(
				$this->optionAttribs,
				array('value' => $record[$this->fieldValue]));
	 
			if (isset($this->selectedValue) &&
			    $record[$this->fieldValue] == $this->selectedValue)
			{
				$optionAttribs['selected'] = 'selected';
			}
	 
			$optionElements[] =
				array('option', $optionAttribs, $record[$this->fieldText]);
		}

		return array('select', $this->attribs, $optionElements);
	}

	/**
	 * Set the options that we are selecting between.
	 *
	 * @param mixed[] The options to select between.
	 */
	public function setOptions($options)
	{
		if (empty($options))
		{
			throw new InvalidArgumentException(
				'needs options to be valid XHTML');
		}
		elseif (!is_array($options) && !$options instanceof Traversable)
		{
			throw new InvalidArgumentException('needs traversable options.');
		}
		
		$this->options = $options;
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
