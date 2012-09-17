<?php
namespace Evoke\Persistence\DB\Table;

use Evoke\Persistence\DB\SQLIface,
	InvalidArgumentException,
	OutOfRangeException;

/**
 * Info
 *
 * Info provides an interface to gather information about a DB table.
 *
 * @author Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license MIT
 * @package Persistence
 */
class Info implements InfoIface
{
	/**
	 * The create information for the database table.
	 * @var string
	 */
	private $createInfo;

	/**
	 * The description information for the database table.
	 * @var string
	 */
	private $description;

	/**
	 * The fields for the database table.
	 * @var mixed[]
	 */
	private $fields;

	/**
	 * The primary keys for the database table.
	 * @var string[]
	 */
	private $primaryKeys;

	/**
	 * The required fields for the database table.
	 * @var string[]
	 */
	private $requiredFields;

	/**
	 * SQL object.
	 * @var Evoke\Persistence\DB\SQLIface
	 */
	protected $sql;

	/**
	 * The table name for the table we are retrieving information for.
	 * @var string
	 */
	protected $tableName;

	/**
	 * Construct a Table Info object.
	 *
	 * @param Evoke\Persistence\DB\SQLIface SQL object.
	 * @param string                        Table Name.
	 */
	public function __construct(SQLIface     $sql,
	                            /* String */ $tableName)
	{
		if (!is_string($tableName))
		{
			throw new InvalidArgumentException(
				__METHOD__ . ' requires tableName as string');
		}

		$this->sql       = $sql;
		$this->tableName = $tableName;
      
		$this->createInfo = $this->sql->getSingleValue(
			'SHOW CREATE TABLE ' . $this->tableName,
			array(),
			1);
      
		$this->description = $this->sql->getAssoc(
			'DESCRIBE ' . $this->tableName);
      
		$this->calculateFields();
		$this->calculateRequiredFields();
		$this->calculateKeyInfo();
	}

	/******************/
	/* Public Methods */
	/******************/

	/**
	 * Get the description of the database table.
	 *
	 * @return string
	 */
	public function getDescription()
	{
		return $this->description;
	}
	
	/**
	 * Get the fields in the database table.
	 *
	 * @return mixed[]
	 */
	public function getFields()
	{
		return $this->fields;
	}

	/**
	 * Get the foreign keys.
	 *
	 * @return mixed[]
	 */
	public function getForeignKeys()
	{
		return $this->foreignKeys;
	}
   
	/**
	 * Get the primary keys.
	 *
	 * @return mixed[]
	 */
	public function getPrimaryKeys()
	{
		return $this->primaryKeys;
	}

	/**
	 * Get the required fields.
	 *
	 * @return mixed[]
	 */
	public function getRequired()
	{
		return $this->requiredFields;
	}

	/**
	 * Get the table name.
	 *
	 * @return string
	 */
	public function getTableName()
	{
		return $this->tableName;
	}
   
	/**
	 * Get the type of the specified field.
	 *
	 * @return string
	 */
	public function getType($field)
	{
		if (!in_array($field, $this->fields))
		{
			throw new OutOfRangeException(
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

	/**
	 * Get the types of all of the fields in the table.
	 *
	 * @return mixed[]
	 */
	public function getTypes()
	{
		$types = array();
      
		foreach($this->description as $entry)
		{
			$types[$entry['Field']] = $entry['Type'];
		}

		return $types;
	}

	/**
	 * Return whether the database requires the field.
	 *
	 * @param string The field to check.
	 *
	 * @return bool
	 */
	public function isRequired($field)
	{
		if (!in_array($field, $this->fields))
		{
			throw new OutOfRangeException(
				__METHOD__ . 'Unknown field: ' . $field . ' for table: ' .
				$this->tableName);
		}

		return (!empty($this->requiredFields) &&
		        in_array($field, $this->requiredFields));
	}
   
	/*********************/
	/* Protected Methods */
	/*********************/

	/**
	 * Store the fields from the table into the object.
	 */
	protected function calculateFields()
	{
		$this->fields = array();

		foreach ($this->description as $entry)
		{
			$this->fields[$entry['Field']] = $entry['Field'];
		}
	}
   
	/**
	 * Get the fields that are required for an entry to be made to the database
	 * and store them in the object.
	 */
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

	/**
	 * Get the key information from the database and store it in the object.
	 */
	protected function calculateKeyInfo()
	{
		$primaryStr = 'PRIMARY KEY';
		$foreignStr = 'FOREIGN KEY';

		// Set the key arrays to blank.
		$this->primaryKeys = array();
		$this->foreignKeys = array();

		$createInfoArr = explode("\n", $this->createInfo);
		$primaryKeyLinesArr = preg_grep('/' . $primaryStr . '/',
		                                $createInfoArr);
		$foreignKeyLinesArr = preg_grep('/' . $foreignStr . '/',
		                                $createInfoArr);

		foreach ($primaryKeyLinesArr as $primaryKeyLine)
		{
			if (preg_match('(' . $primaryStr . '\s*\(([^\)]+)\))i',
			               $primaryKeyLine,
			               $matches))
			{
				$matchedKeys = explode(',', $matches[1]);

				foreach ($matchedKeys as $pKey)
				{
					$this->primaryKeys[] = trim($pKey, '` ');
				}
			}
		}

		foreach ($foreignKeyLinesArr as $foreignKeyLine)
		{
			preg_match(
				'/.*' . $foreignStr . '\s*\(`([^`]*)`\) REFERENCES `([^`]*)` ' .
				'\(`([^`]*)`\)/',
				$foreignKeyLine,
				$keyArr);
	 
			$this->foreignKeys[$keyArr[1]]
				= array('Foreign_Table' => $keyArr[2],
				        'Foreign_Field' => $keyArr[3]);
		}
	}
}
// EOF