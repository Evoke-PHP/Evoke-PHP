<?php
/**
 * Checkboxes View
 *
 * @package View
 */
namespace Evoke\View\Form;

use Evoke\View\ViewIface,
	InvalidArgumentException;

/**
 * Checkboxes View
 *
 * @author Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license MIT
 * @package View
 */
class Checkboxes implements ViewIface
{
	/**
	 * The attributes for the fieldset that surrounds the checkboxes.
	 * @var string[]
	 */
	protected $attribs;
	
	/**
	 * The text if there are no checkboxes.
	 * @var string
	 */
	protected $emptyText;

	/**
	 * Prefix for the id attribute of the checkboxes.
	 * @var string
	 */
	protected $prefix;

	/**
	 * The field to use for the checkbox text.
	 * @var string|int
	 */
	protected $textField;

	/**
	 * The field that defines the value of the checkbox.
	 * @var string|int
	 */
	protected $valueField;

	/**
	 * Construct a Checkboxes View.
	 *
	 * @param string|int Text field.
	 * @param string|int Value field.
	 * @param string[]   Fieldset attributes.
	 * @param string     Empty Text when there are no checkboxes.
	 * @param string     Prefix for the ID attribute.
	 */
	public function __construct(
		/* string|int */ $textField,
		/* string|int */ $valueField = 'ID',
		/* string[]   */ $attribs    = array(),
		/* string     */ $emptyText  = 'No options available',
		/* string     */ $prefix     = '')
	{
		$this->attribs    = $attribs;
		$this->emptyText  = $emptyText;
		$this->prefix     = $prefix;
		$this->textField  = $textField;
		$this->valueField = $valueField;
	}

	/**
	 * Get the view of the checkboxes from the data supplied.
	 *
	 * @param Array[] The checkbox data of the form:
	 * <pre><code>
	 * array('Checkboxes' => array()  // Array of records.
	 *       'Selected'   => array()) // Array of values that are checked.
	 * </code></pre>
	 */
	public function get(Array $data = array())
	{
		$data += array('Checkboxes'    => NULL,
		               'Selected'      => NULL);
		            
		if (!is_array($data['Checkboxes']))
		{
			throw new InvalidArgumentException('needs Checkboxes as array');
		}

		if (!is_array($data['Selected']))
		{
			throw new InvalidArgumentException('needs Selected as array');
		}
      
		if (empty($data['Checkboxes']))
		{
			// Build an element to show that there are no checkboxes defined.
			return array('fieldset',
			             $this->attribs,
			             array(array('div',
			                         array('class' => 'No_Elements'),
			                         $this->emptyText)));
		}

		$checkboxElems = array();
      
		foreach ($data['Checkboxes'] as $key => $record)
		{
			if (!isset($record[$this->textField]) ||
			    !isset($record[$this->valueField]))
			{
				throw new InvalidArgumentException(
					' Record: ' . var_export($record, true) . ' at key: ' .
					$key . ' does not contain the required fields ' .
					'Text_Field: ' . $this->textField . ' and Value_Field: ' .
					$this->valueField);
			}
	 
			$id = $this->prefix . $record[$this->valueField];
			$isSelected = array();
	 
			if (in_array($record[$this->valueField], $data['Selected']))
			{
				$isSelected = array('checked' => 'checked');
			}
	 
			$checkboxElems[] = array('label',
			                         array('for' => $id),
			                         $record[$this->textField]);
			$checkboxElems[] = array('input',
			                         array_merge(array('type' => 'checkbox',
			                                           'id'   => $id,
			                                           'name' => $id),
			                                     $isSelected));
		}
      
		return array('fieldset', $this->attribs, $checkboxElems);
	}
}
// EOF