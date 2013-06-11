<?php
/**
 * Validation
 *
 * @package Persistence
 */
namespace Evoke\Persistence\DB;

use Evoke\Message\TreeIface,
	Evoke\Persistence\DB\InfoIface,
	Evoke\Service\ValidationIface;

/**
 * Validation
 *
 * @author Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license MIT
 * @package Persistence
 */
class Validation implements ValidationIface
{
	/**
	 * Failures
	 * @var Evoke\Message\TreeIface
	 */
	protected $failures;

	/**
	 * Table Info object.
	 * @var Evoke\Persistence\DB\InfoIface
	 */
	protected $tableInfo;
	
	/**
	 * Construct a Table Validator object.
	 *
	 * @param Evoke\Persistence\DB\InfoIface Table Info object.
	 * @param Evoke\Message\TreeIface        Failures.
	 */
	public function __construct(TableInfoIface $tableInfo,
	                            TreeIface      $failures)
	{
		$this->failures  = $failures;
		$this->tableInfo = $tableInfo;
	}

	/******************/
	/* Public Methods */
	/******************/

	/**
	 * Get a the last failures from a validation.
	 *
	 * @return The failure object.
	 */
	public function getFailures()
	{
		return $this->failures;
	}
	
	/**
	 * Check whether a set of fields would be valid.
	 *
	 * @param mixed[]  The set of fields to check.
	 * @param string[] Any fields that should be ignored in the calculation of
	 *                 the validity.
	 *
	 * @return bool    Whether the fieldset is valid. If the return is false
	 *                 `getFailures` can be used to retrieve the errors.
	 */
	public function isValid($fieldset, $ignoredFields=array())
	{
		$this->failures->reset();

		if (!is_array($fieldset))
		{
			$this->failures->add('Data', 'Format_Error');
			return false;
		}

		$requiredFields = $this->tableInfo->getRequired();
		
		// Check that all required keys are included in the fieldset.
		foreach ($requiredFields as $reqField)
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
			$fullType = $this->tableInfo->getType($key);
			$required = $this->tableInfo->isRequired($key);
			$type = strtoupper(preg_replace("/\(.*\)$/", '', $fullType));
			$subType = preg_replace("/^.*\((.*)\).*$/", "$1", $fullType);

			// 1 is removed later as it is used to store the text or blob.
			$textBlobLength = 1;
	 
			/// @todo range checking for numeric types.
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
	
	/**
	 * Generic validity checking for a database field.  Check that required
	 * values are present.
	 *
	 * @param string The field name.
	 * @param string The value.
	 * @param bool   Whether the field is required.
	 *
	 * @returns bool Whether the field is valid.
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

	/**
	 * Check that a text field is within its length limit.
	 *
	 * @param string The field name.
	 * @param string The value.
	 * @param bool   Whether the field is required.
	 * @param string The subtype of the field.
	 *
	 * @return bool
	 */
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

	/// @todo Range checking.
	/**
	 * Check that the value is an integer within the required bounds.
	 *
	 * @param string The field name.
	 * @param string The value.
	 * @param bool   Whether the field is required.
	 * @param string The subtype of the field.
	 *
	 * @return bool
	 */
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
   
	/// @todo range checking.
	/**
	 * Check that the value is a float within the required bounds.
	 *
	 * @param string The field name.
	 * @param string The value.
	 * @param bool   Whether the field is required.
	 * @param string The subtype of the field.
	 *
	 * @return bool
	 */	 
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

		// Check for overflow of the integer portion of the number. Decimal
		// place overflow is ignored but this function could be overriden if it
		// is required.
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

	/// @todo Improve the check.
	/**
	 * Do a crude check on the date to ensure it is valid.
	 *
	 * @param string The field name.
	 * @param string The value.
	 * @param bool   Whether the field is required.
	 * @param string The subtype of the field.
	 *
	 * @return bool
	 */	 
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

	/**
	 * Check that the value is within the set of accepted values.
	 *
	 * @param string The field name.
	 * @param string The value.
	 * @param bool   Whether the field is required.
	 * @param string The subtype of the field.
	 *
	 * @return bool
	 */	 
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
