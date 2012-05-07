<?php
namespace Evoke\DB\Table;

use Evoke\Iface;

/// Info provides an interface to gather information about a DB table.
class Info implements Iface\DB\Table\Info
{ 
	/** @property $createInfo
	 *  The create information \string for the database table.
	 */
	private $createInfo;

	/** @property $description
	 *  The description information \string for the database table.
	 */
	private $description;

	/** @property $failures
	 *  The failures MessageArray \object
	 */
	protected $failures;

	/** @property $fields
	 *  The fields \array for the database table.
	 */
	private $fields;

	/** @property $primaryKeys
	 *  The primary keys \array for the database table.
	 */
	private $primaryKeys;

	/** @property $requiredFields
	 *  The required fields \array for the database table.
	 */
	private $requiredFields;

	/** @property $sql
	 *  SQL \object
	 */
	protected $sql;

	/** @property $tableName
	 *  The table name \string for the table we are retrieving information for.
	 */
	protected $tableName;
   
	public function __construct(Iface\DB\SQL      $sql,
	                            /* String */      $tableName,
	                            /// \todo Fix MessageTree dependency.
	                            Iface\MessageTree $failures = NULL)
	{
		if (!is_string($tableName))
		{
			throw new \InvalidArgumentException(
				__METHOD__ . ' requires tableName as string');
		}

		$this->failures  = $failures;
		$this->sql       = $sql;
		$this->tableName = $tableName;
      
		$this->createInfo = $this->sql->getSingleValue(
			'SHOW CREATE TABLE ' . $this->tableName,
			array(),
			1);
      
		$this->description = $this->sql->getAssoc('DESCRIBE ' . $this->tableName);
      
		$this->calculateFields();
		$this->calculateRequiredFields();
		$this->calculateKeyInfo();
	}

	/******************/
	/* Public Methods */
	/******************/

	/// Get the description of the database table.
	public function getDescription()
	{
		return $this->description;
	}
   
	/// Get the fields in the database table.
	public function getFields()
	{
		return $this->fields;
	}

	/// Get the foreign keys.
	public function getForeignKeys()
	{
		return $this->foreignKeys;
	}
   
	/// Get the primary keys.
	public function getPrimaryKeys()
	{
		return $this->primaryKeys;
	}

	/// Get the required fields.
	public function getRequired()
	{
		return $this->requiredFields;
	}

	/// Get the table name.
	public function getTableName()
	{
		return $this->tableName;
	}
   
	/// Get the type of the specified field.
	public function getType($field)
	{
		if (!in_array($field, $this->fields))
		{
			throw new \OutOfRangeException(
				__METHOD__ . 'Unknown field: ' . $field . ' for table: ' .
				$this->tableName);
		}
      
		$type = '';

		foreach ($this->description as $entry)
		{
			if ($entry['Field'] == $field)
			{
				$type = $entry['Type'];
				break;
			}
		}

		return $type;
	}

	/// Get the types of all of the fields in the table.
	public function getTypes()
	{
		$types = array();
      
		foreach($this->description as $entry)
		{
			$types[$entry['Field']] = $entry['Type'];
		}

		return $types;
	}

	/// Return whether the database requires the field.
	public function isRequired($field)
	{
		if (!in_array($field, $this->fields))
		{
			throw new \OutOfRangeException(
				__METHOD__ . 'Unknown field: ' . $field . ' for table: ' .
				$this->tableName);
		}

		return (!empty($this->requiredFields) &&
		        in_array($field, $this->requiredFields));
	}
   
	/** Get a copy of the failure array object showing the last failures from an
	 *  action.
	 *  \return The failure array object.
	 */
	public function getFailures()
	{
		return $this->failures;
	}

	/** Check whether a set of fields would be valid for an insert or delete
	 *  statement.  
	 *  @param fieldset \array The set of fields to check.
	 *  @param ignoredFields \array Any fields that should be ignored in the
	 *  calculation of the validity.
	 *  \return A \bool of whether the fieldset is valid for an insert or
	 *  delete statement. If the return is false \ref getFailures can be used
	 *  to retrieve the errors.
	 */
	public function isValid($fieldset, $ignoredFields=array())
	{
		$this->failures->reset();

		if (!is_array($fieldset))
		{
			$this->failures->add('Data', 'Format_Error');
			return false;
		}

		// Check that all required keys are included in the fieldset.
		foreach ($this->requiredFields as $reqField)
		{
			if (!in_array($reqField, $ignoredFields) &&
			    !array_key_exists($reqField, $fieldset))
			{
				$this->failures->add($reqField, 'Required_Field_Error');
			}
		}
      
		// Remove the ignored fields from the calculation if they are set.
		foreach ($ignoredFields as $ignoredField)
		{
			if (isset($fieldset[$ignoredField]))
			{
				unset($fieldset[$ignoredField]);
			}
		}

		// Check each field in the fieldset to ensure it is valid.
		foreach ($fieldset as $key => $val)
		{
			$fullType = $this->getType($key);
			$required = $this->isRequired($key);
			$type = strtoupper(preg_replace("/\(.*\)$/", '', $fullType));
			$subType = preg_replace("/^.*\((.*)\).*$/", "$1", $fullType);

			$passMatch = '';
			$failMatch = '';

			// 1 is removed later as it is used to store the text or blob.
			$textBlobLength = 1;
	 
			/// \todo range checking for numeric types.
			switch($type)
			{
			case('LONGTEXT'):
			case('LONGBLOB'):
				$textBlobLength *= 256;
			case('MEDIUMTEXT'):
			case('MEDIUMBLOB'):
				$textBlobLength *= 256;
			case('TEXT'):
			case('BLOB'):
				$textBlobLength *= 256;
			case('TINYTEXT'):
			case('TINYBLOB'):
				$textBlobLength *= 256;
			case('CHAR'):
			case('VARCHAR'):
				$textLength = $textBlobLength - 1 + $subType;

				$this->isValidText($key, $val, $required, $textLength);
				break;

			case('TINYINT'):
			case('SMALLINT'):
			case('MEDIUMINT'):
			case('INT'):
			case('BIGINT'):
				$this->isValidInt($key, $val, $required, $subType);
				break;

			case('FLOAT'):
			case('DOUBLE'):
			case('DECIMAL'):
				$this->isValidFloat($key, $val, $required, $subType);
				break;

			case('YEAR'):
			case('DATE'):
			case('TIME'):
			case('DATETIME'):
			case('TIMESTAMP'):
				$this->isValidDate($key, $val, $required, $type, $subType);
				break;

			case('ENUM'):
			case('SET'):
				$this->isValidSet($key, $val, $required, $subType);
				break;

			default:
				$this->failures->add($key, 'Unknown_Type_Error');
			}
		}

		return $this->failures->isEmpty();
	}
   
	/*********************/
	/* Protected Methods */
	/*********************/

	/// Store the fields from the table into the object.
	protected function calculateFields()
	{
		$this->fields = array();

		foreach ($this->description as $entry)
		{
			$this->fields[$entry['Field']] = $entry['Field'];
		}
	}
   
	/// Get the fields that are required for an entry to be made to the database
	//  and store them in the object.
	protected function calculateRequiredFields()
	{
		$this->requiredFields = array();

		foreach ($this->description as $entry)
		{
			if ($entry['Null'] == 'NO' &&
			    !preg_match("/auto_increment/", $entry['Extra']))
			{
				$this->requiredFields[] = $entry['Field'];
			}
		}
	}

	/// Get the key information from the database and store it in the object.
	protected function calculateKeyInfo()
	{
		$pK_STR = 'PRIMARY KEY';
		$fK_STR = 'FOREIGN KEY';

		// Set the key arrays to blank.
		$this->primaryKeys = array();
		$this->foreignKeys = array();

		$createInfoArr = explode("\n", $this->createInfo);
		$primaryKeyLinesArr = preg_grep("/$pK_STR/", $createInfoArr);
		$foreignKeyLinesArr = preg_grep("/$fK_STR/", $createInfoArr);

		foreach ($primaryKeyLinesArr as $primaryKeyLine)
		{
			preg_match('/.*' . $pK_STR . '\s*\(`([^`]*)`\)/',
			           $primaryKeyLine,
			           $matches);

			// Ignore the full match.
			$matchedKeys = array_slice($matches, 1);

			foreach ($matchedKeys as $pKey)
			{
				$this->primaryKeys[] = $pKey;
			}
		}

		foreach ($foreignKeyLinesArr as $foreignKeyLine)
		{
			preg_match(
				'/.*' . $fK_STR . '\s*\(`([^`]*)`\) REFERENCES `([^`]*)` ' .
				'\(`([^`]*)`\)/',
				$foreignKeyLine,
				$keyArr);
	 
			$this->foreignKeys[$keyArr[1]]
				= array('Foreign_Table' => $keyArr[2],
				        'Foreign_Field' => $keyArr[3]);
		}
	}

	/** Generic validity checking for a database field.
	 *  Check that required values are present.
	 *  \returns Whether the field is valid.
	 */
	protected function isValidGeneric($key, $val, $required)
	{
		if ($required && empty($val))
		{
			$this->failures->add($key, 'Required_Field_Error');
			return false;
		}
		else
		{
			return true;
		}
	}

	/// Check that a text field is within its length limit.
	protected function isValidText($key, $val, $required, $subType)
	{
		if (!$this->isValidGeneric($key, $val, $required))
		{
			return false;
		}
      
		if (strlen($val) > $subType)
		{
			$this->failures->add($key, 'Overflow_Error');
			$this->failures->add($key, 'STRLEN: ' . var_export($val, true) .
			                              ' Allowed: ' . var_export($subType, true));
			return false;
		}
		else
		{
			return true;
		}
	}

	/// \todo Range checking.
	/// Check that the value is an integer within the required bounds.
	protected function isValidInt($key, $val, $required, $subType)
	{
		if (!$this->isValidGeneric($key, $val, $required))
		{
			return false;
		}
      
		// Empty non-required (generically valid) fields are valid.
		if (empty($val))
		{
			return true;
		}
      
		if (!empty($subType))
		{
			$repetitions = '{1,' . $subType .'}';
		}
		else
		{
			$repetitions = '+';
		}

		if (!preg_match('/^[0-9]' . $repetitions . '$/', $val))
		{
			$this->failures->add($key, 'Overflow_Error');
			return false;
		}
		else
		{
			return true;
		}
	}
   
	/// \todo range checking.
	/// Check that the value is a float within the required bounds.
	protected function isValidFloat($key, $val, $required, $subType)
	{
		if (!$this->isValidGeneric($key, $val, $required))
		{
			return false;
		}

		// Empty non-required (generically valid) fields are valid.
		if (empty($val))
		{
			return true;
		}

		// Any numbers should be able to be handled by the a float type field.
		if (!is_numeric($val))
		{
			$this->failures->add($key, 'Format_Error');
			return false;
		}

		// Check for overflow of the integer portion of the number. Decimal place
		// overflow is ignored but this function could be overriden if it is
		// required.
		$floatSpec = explode(',', $subType);
		$totalDigits = $floatSpec[0];
		$decimalPlaces = 0;
      
		if (isset($floatSpec[1]))
		{
			$decimalPlaces = $floatSpec[1];
		}

		$maxInt = intval(str_repeat('9', $totalDigits - $decimalPlaces));
		$actualInt = intval(round(floatval($val), $decimalPlaces));
      
		if (abs($actualInt) > abs($maxInt))
		{
			$this->failures->add($key, 'Overflow_Error');
			return false;
		}

		return true;
	}

	/// \todo Improve the check.
	/// Do a crude check on the date to ensure it is valid.
	protected function isValidDate($key, $val, $required, $type, $subType)
	{
		if (!$this->isValidGeneric($key, $val, $required))
		{
			return false;
		}
	    
		// Empty non-required (generically valid) fields are valid.
		if (empty($val))
		{
			return true;
		}
      
		// This is a very crude match.
		if (!preg_match("/[0-9:\.]+/", $val))
		{
			$this->failures->add($key, 'Format_Error');
			return false;
		}
		else
		{
			return true;
		}
	}

	/// Check that the value is within the set of accepted values.
	protected function isValidSet($key, $val, $required, $subType)
	{
		if (!$this->isValidGeneric($key, $val, $required))
		{
			return false;
		}
	    
		// Empty non-required (generically valid) fields are valid.
		if (empty($val))
		{
			return true;
		}      

		// Remove the first and last quotes.
		if (strlen($subType) > 1)
		{
			$subType = substr($subType, 1, strlen($subType) - 2);
		}

		$setArr = explode('\',\'', $subType);
      
		if (!in_array($val, $setArr))
		{
			$this->failures->add($key, 'Range_Error');
			return false;
		}

		return true;
	}
}
// EOF