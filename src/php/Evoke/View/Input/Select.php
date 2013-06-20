<?php
/**
 * Select Input View
 *
 * @package View\Input
 */
namespace Evoke\View\Input;

use Evoke\View\ViewIface,
	InvalidArgumentException,
	RuntimeException;

/**
 * Select Input View
 *
 * @author    Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license   MIT
 * @package   View
 */
class Select implements ViewIface
{
	/**
	 * Protected properties.
	 *
	 * @var string[] $attribs       Attributes for the select element.
	 * @var string[] $optionAttribs Attributes for each select option.
	 * @var string   $textField     Field in the data for the option text.
	 * @var string   $valueField    Field in the data for the option value.
	 */
	protected $attribs, $optionAttribs, $textField, $valueField;

	/**
	 * Construct a Select view.
	 *
	 * @param string   Field from the data for the option text.
	 * @param string   Field from the data for the option value.
	 * @param string[] Attributes for the select element.
	 * @param string[] Attributes for the option elements.
	 */
	public function __construct(/* String */ $textField,
	                            /* String */ $valueField    = 'ID',
	                            Array        $attribs       = array(),
	                            Array        $optionAttribs = array())
	{
		if (!is_string($textField))
		{
			throw new InvalidArgumentException(
				__METHOD__ . ' needs textField as string');
		}

		if (!is_string($valueField))
		{
			throw new InvalidArgumentException(
				__METHOD__ . ' needs valueField as string');
		}

		$this->attribs       = $attribs;
		$this->optionAttribs = $optionAttribs;
		$this->textField     = $textField;
		$this->valueField    = $valueField;
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
			throw new RuntimeException(
				__METHOD__ . ' cannot set select element without having ' .
				'options to select from (The XHTML would be invalid).');
		}

		$optionElements = array();

		foreach ($this->data as $key => $record)
		{
			if (!isset($record[$this->textField]) ||
			    !isset($record[$this->valueField]))
			{
				throw new InvalidArgumentException(
					__METHOD__ . ' Record: ' . var_export($record, true) .
					' at key: ' . $key . ' does not contain the required ' .
					'fields Text_Field: ' . $this->textField .
					' and Value_Field: ' . $this->valueField);
			}

			$value = $record[$this->valueField];
			$optionAttribs = array_merge($this->optionAttribs,
			                             array('value' => $value));
	 
			if (isset($this->params['Selected']) &&
			    $value == $this->params['Selected'])
			{
				$optionAttribs['selected'] = 'selected';
			}
	 
			$optionElements[] =
				array('option', $optionAttribs, $record[$this->textField]);
		}

		return array('select', $this->attribs, $optionElements);
	}
}
// EOF