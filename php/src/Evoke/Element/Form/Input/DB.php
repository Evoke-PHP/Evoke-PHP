<?php
namespace Evoke\Element\Input;
/** Element_DB_Input provide elements for input to the database.
 * Make an element from the database describe table information.
 * @param fieldInfo \array A single row from the describe table information.
 *
 *  Below is an example of the describe table information (A single row from
 *  this provides the field information):
 *  \verbatim
 +-------+--------------+------+-----+---------+----------------+
 | Field | Type         | Null | Key | Default | Extra          |
 +-------+--------------+------+-----+---------+----------------+
 | ID    | int(11)      | NO   | PRI | NULL    | auto_increment |
 | Name  | varchar(100) | YES  |     | 'Smith' |                |
 +-------+--------------+------+-----+---------+----------------+
 \endverbatim
*/
class DB extends \Evoke\Element
{
	/** @property $encasing
	 *  \bool Whether the input is to be encased within a div element.
	 */
	protected $encasing;

	/** @property $fieldAttribs
	 *  \array of attributes for the field.
	 */
	protected $fieldAttribs;

	/** @property $fieldInfo
	 *  \array Information for the field.
	 */
	protected $fieldInfo;

	/** @property $fieldPrefix
	 *  Prefix \string to be used for the name attribute of the field.
	 */
	protected $fieldPrefix;

	/** @property $fieldValue
	 *  \string The value of the input (the text within it).
	 */
	protected $fieldValue;

	/** @property $hidden
	 *  \bool Whether the input is hidden.
	 */
	protected $hidden;

	/** @property $highlighted
	 *  \bool Whether the input is highlighted.
	 */
	protected $highlighted;

	/** @property $id
	 *  \string The id attribute for the input.
	 */
	protected $id;

	/** @property $label
	 *  \string The label for the input.
	 */
	protected $label;

	/** @property $requiredIndication
	 *  \bool Whether there should be a required indication for the input (*).
	 */
	protected $requiredIndication;

	/** @property $translateLabel
	 *  \bool Whether the label should be translated.
	 */
	protected $translateLabel;

	/** @property $translatePrefix
	 *  \string The prefix to use for the translation.
	 */
	protected $translatePrefix;

	/** @property $translator
	 *  Translator \object
	 */
	protected $translator;

	public function __construct(Array $setup)
	{
		$setup += array('Attribs'             => array('class' => 'DB_Input'),
		                'Encasing'            => true,
		                'Field_Attribs'       => array(),
		                'Field_Info'          => NULL,
		                'Field_Prefix'        => '',
		                'Field_Value'         => NULL,
		                'Hidden'              => false,
		                'Highlighted'         => false,
		                'ID'                  => NULL,
		                'Label'               => NULL,
		                'Required_Indication' => true,
		                'Translate_Label'     => true,
		                'Translate_Prefix'    => '',
		                'Translator'          => NULL);

		if (!$translator instanceof \Evoke\Core\Iface\Translator)
		{
			throw new \InvalidArgumentException(__METHOD__ . ' needs Translator');
		}
      
		parent::__construct($setup);

		$this->attribs      		  = $attribs;
		$this->encasing     		  = $encasing;
		$this->fieldAttribs 		  = $fieldAttribs;
		$this->fieldInfo    		  = $fieldInfo;
		$this->fieldPrefix  		  = $fieldPrefix;
		$this->fieldValue   		  = $fieldValue;
		$this->hidden       		  = $hidden;
		$this->highlighted  		  = $highlighted;
		$this->id           		  = $iD;
		$this->label        		  = $label;
		$this->requiredIndication = $requiredIndication;
		$this->translateLabel     = $translateLabel;
		$this->translatePrefix    = $translatePrefix;
		$this->translator         = $translator;
	}

	/******************/
	/* Public Methods */
	/******************/

	public function set(Array $data)
	{
		if (!isset($data['Field_Info']))
		{
			throw new \InvalidArgumentException(
				__METHOD__ . ' needs Field_Info');
		}

		
		return parent::set(array($this->tag,
		                         array(),
		                         $this->getElements()));
	}
   
	/*********************/
	/* Protected Methods */
	/*********************/

	/// Build the label for the field.
	protected function buildLabel($field, $id=NULL, $type='TEXT')
	{
		$attribs = array();

		// Create an appropriate label.
		if ($type === 'SET')
		{
			$tag = 'legend';
		}
		else
		{
			$tag = 'label';
	 
			if (isset($id))
			{
				$attribs['for'] = $id;
			}
		}
      
		// Set the label by a specifically passed label, translate it from the
		// field name or use the field name as a last resort.
		if (isset($this->label))
		{
			$text = $this->label;
		}
		elseif ($this->translateLabel)
		{
			$text = $this->translator->get(
				$this->translatePrefix . $field);
		}
		else
		{
			$text = $field;
		}

		return array($tag, $attribs, $text);
	}

	/// Build the required indication.
	protected function buildRequiredIndication($required)
	{
		if ($required)
		{
			$indicationAttribs = array('class' => 'Required');
			$indicationText = array('Text' => '*');
		}
		else
		{
			$indicationAttribs = array('class' => 'Optional');
			$indicationText = array('Text' => '');
		}
      
		return array('span', $indicationAttribs, $indicationText);
	}

	// Get the input elements for the field
	protected function getElements()
	{
		$fieldInfo = $this->fieldInfo;
      
		// The elements that make up the DB Element that is being built.
		$elems = array();

		$field = $fieldInfo['Field'];
		$value = '';
      
		if (isset($this->fieldValue))
		{
			$value = $this->fieldValue;
		}

		// Get the base attributes defaulting the id and name for the inputs.
		$attribs = array('name' => $this->fieldPrefix . $field);

		if (isset($this->iD))
		{
			$attribs['id'] = $this->iD;
		}

		$attribs = array_merge($attribs, $this->fieldAttribs);
      
		// Highlight fields that should be.
		if (isset($this->highlighted[$field]))
		{
			if (isset($attribs['class']))
			{
				$attribs['class'] .= ' Highlighted';
			}
			else
			{
				$attribs['class'] =  'Highlighted';
			}
		}
      
		// Ensure a password input is used for password fields.
		if (strtoupper($field) == 'PASSWORD')
		{
			$attribs['type'] = 'password';
		}

		if ($this->isHidden($fieldInfo))
		{
			$attribs['type'] = 'hidden';
		}

		/// Split the type field into a type and a subtype.
		/// The type is the basic type of the element.
		$type = strtoupper(preg_replace("/\(.*\)$/", '', $fieldInfo['Type']));

		/// The subtype either specifies the length of a field or the options a
		/// field can contain.
		// Get the subType as a simple number representing the length of an item.
		$subType = preg_replace("/.*\(([0-9]*)\)$/", "$1", $fieldInfo['Type']);
		// Get the subTypeArr as a list of options for an ENUM or SET type.
		// Match the enums or sets in ENUM('a','b','c')
		$subTypeArr = explode(
			',', preg_replace("/.*\((.*)\)$/", "$1", $fieldInfo['Type']));

		foreach ($subTypeArr as $subKey => $subVal)
		{
			$subTypeArr[$subKey] = trim($subVal, '\'');
		}

		// Add a label element where required.
		if (!$this->isHidden($fieldInfo))
		{
			$elems[] = $this->buildLabel($field, $this->iD, $type);
	 
			if ($this->requiredIndication)
			{
				$required = ($fieldInfo['Null'] === 'NO');
				$elems[] = $this->buildRequiredIndication($required);
			}
		}

		// Set the text lines for text areas.
		$textAreaLines = 2;
      
		switch ($type)
		{
		case('CHAR'):
		case('VARCHAR'):
			$elems[] = array(
				'input',
				array_merge(array('type'      => 'text',
				                  'name'      => $field,
				                  'size'      => $subType,
				                  'maxlength' => $subType,
				                  'value'     => $value),
				            $attribs));
			break;
	 
			// Let the code flow through the switch to make the number of lines
			// increase for the biggest textareas.	 
		case('LONGTEXT'):
		case('LONGBLOB'):
			$textAreaLines++;
		case('MEDIUMTEXT'):
		case('MEDIUMBLOB'):
			$textAreaLines++;
		case('TEXT'):
		case('BLOB'):
			$textAreaLines++;
		case('TINYTEXT'):
		case('TINYBLOB'):
			$elems[] = array('textarea',
			                 array_merge(array('name' => $field,
			                                   'rows' => $textAreaLines,
			                                   'cols' => 50),
			                             $attribs),
			                 $value);
			break;

		case('BOOL'):
		case('BIT'):
			if ($value === 1 || $value === true)
			{
				$checked = array('checked' => 'checked');
			}
			else
			{
				$checked = array();
			}
	 
			$elems[] = array('input',
			                 array_merge(array('type' => 'checkbox',
			                                   'name' => $field),
			                             $checked,
			                             $attribs));
			break;
	 
		case('TINYINT'):
		case('SMALLINT'):
		case('MEDIUMINT'):
		case('INT'):
		case('BIGINT'):
		case('DATE'):
		case('DATETIME'):
		case('TIMESTAMP'):
		case('TIME'):
		case('YEAR'):
			$elems[] = array('input',
			                 array_merge(array('type'      => 'text',
			                                   'name'      => $field,
			                                   'size'      => $subType,
			                                   'maxlength' => $subType,
			                                   'value'     => $value),
			                             $attribs));
			break;

		case('FLOAT'):
		case('DOUBLE'):
		case('DECIMAL'):
			$entryLength = '';
			$displayLength = '';
	 
			if (count($subTypeArr) === 2)
			{
				// The length for a float field input should be enough to easily
				// input the required value.  A float can be input in many forms
				// that increase the required characters such as:
				//   -1234567e-3 (11 chars) vs 1234.567 (8 chars) vs
				//   -123456.7e-2 (12 chars)
	    
				// We allow entry of a sign character, decimal character, exponent
				// character, exponent sign and 2 exponent digits (6 extras).
				$entryLength = $subTypeArr[0] + 6;

				// We allow display of a sign character and decimal character.
				$displayLength = $subTypeArr[0] + 2;
			}
	 
			$elems[] = array('input',
			                 array_merge(array('type'      => 'text',
			                                   'name'      => $field,
			                                   'size'      => $displayLength,
			                                   'maxlength' => $entryLength,
			                                   'value'     => $value),
			                             $attribs));
			break;
	 
		case('ENUM'):
			$optionElements = array();

			foreach ($subTypeArr as $option)
			{
				// Determine whether the option should be selected.
				$selectedArr = array();

				if ($option == $value)
				{
					$selectedArr = array('selected' => 'selected');
				}
	    
				$optionElements[] =
					array('option',
					      array_merge(array('value' => $option), $selectedArr),
					      $option);
			}

			$elems[] = array(
				'select',
				array_merge(array('name' => $field), $attribs),
				$optionElements);
			break;

		case('SET'):
			foreach ($subTypeArr as $check)
			{
				// Determine whether the checkbox should be set.
				$checkedArr = array();

				if ($check == $value)
				{
					$checkedArr = array('checked' => 'checked');
				}

				$checkboxElements[] =
					array('label', array('for' => $check), $check);
	    
				$checkboxElements[] =
					array('input',
					      array_merge(array('type'  => 'checkbox',
					                        'name'  => $check,
					                        'value' => $check),
					                  $checkedArr));
			}

			$elems[] = array('fieldset', $attribs, $checkboxElements);
			break;

		default:
			throw new \OutOfBoundsException(__METHOD__ . ' Unknown type: ' . $type);
		}

		return $elems;
	}

	/// Hide primary key fields by default.
	protected function isHidden($fieldInfo)
	{
		return ($fieldInfo['Key'] === 'PRI' || $this->hidden);
	}
}
// EOF