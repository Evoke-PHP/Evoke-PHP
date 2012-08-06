<?php
namespace Evoke\Persistence\DB\Table;

use InvalidArgumentException;

/**
 * Join Tree
 *
 * The JoinTree class is primarily used to interact with Relational Databases.
 * Relational databases have tables of data that are linked to other tables
 * via Foreign Keys.  The JoinTree provides a way or representing these
 * relationships so that data can be managed in real world units (with related
 * data joint by the appropriate relationship).
 *
 * Example:
 *    List of products, each of a particular size with a set of related images.
 * 
 * SQL structure (PK = Primary Key, FK = Foreign Key):
 * <pre>
 *   +====================+     
 *   | Product            |     +===============+
 *   +--------------------+     | Image_List    |
 *   | PK | ID            |     +---------------+     +===============+
 *   |    | Name          |     | PK | ID       |     | Image         |
 *   | FK | Image_List_ID |---->|    | List_ID  |     +---------------+
 *   | FK | Size_ID       |-.   | FK | Image_ID |---->| PK | ID       |
 *   +====================+ |   +===============+     |    | Filename |
 *                          |                         +===============+
 *                          |   +===========+
 *                   	    |   | Size      |
 *                   	    |   +-----------+
 *                   	    `-->| PK | ID   |
 *                   	        |    | Name |
 *                   	        +===========+
 * </pre>
 *
 * Joins:
 * <pre><code>
 * array(Table_Name => Product,
 *       Joins => array(
 *	          array(Child_Field  => List_ID,
 *	                Parent_Field => Image_List_ID,
 *	                Table_Name   => Image_List,
 *	                Joins        => array(
 *	                    array(Child_Field => ID,
 *	                          Parent_Field => Image_ID,
 *	                          Table_Name  => Image))),
 *	          array(Child_Field  => ID,
 *	                Parent_Field => Size_ID,
 *	                Table_Name   => Size)));
 * </code></pre>
 *
 * The above is an abstract representation of the Joins tree that would
 * represent the data.  The JoinTree class models the above with its properties
 * and Joins array which contains references to further JoinTree objects.  The
 * methods within the class are used to process the JoinTree.
 *
 * @author Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license MIT
 * @package Persistence
 */
class Joins implements JoinsIface
{
	/**
	 * Whether the current join is administatively managed (for the purposes of
	 * adding, editing or deleting data).
	 * @var bool
	 */
	protected $adminManaged;

	/**
	 * Fields that are handled automatically by the database.
	 * @var string[]
	 */
	protected $autoFields;

	/**
	 * The child field of the join.  This is the name of the field in the table
	 * that this join points to.
	 * @var string
	 */
	protected $childField;

	/**
	 * How a match should be determined between the parent field & child field.
	 * @var string
	 */
	protected $compareType;

	/** 
	 * Separator string to use between IDs in a table with multiple keys.
	 * @var string
	 */
	protected $idSeparator;

	/**
	 * Database Table Info
	 * @var Evoke\Persistence\DB\Table\InfoIface
	 */
	protected $info;
	
	/**
	 * The type of the join ('LEFT JOIN', 'RIGHT JOIN') etc.
	 * @var string
	 */
	protected $joinType;

	/**
	 * Array ofJoin objects from this node to other Join objects in the join
	 * tree.
	 * @var Evoke\Persistence\DB\Table\JoinsIface[]
	 */
	protected $joins;

	/**
	 * The field used to join records together.
	 * @var string
	 */
	protected $jointKey;

	/**
	 * The parent field for the current join.  The parent field is related to
	 * the Join object that is the current node's parent.  It is the field in
	 * that node's Table.
	 * @var string
	 */
	protected $parentField;

	/**
	 * The table name to be used for the current table (Aliases can be used to
	 * disambiguate data).
	 * @var string
	 */
	protected $tableAlias;

	/**
	 * The table name of the current table.
	 * @var string
	 */
	protected $tableName;

	/**
	 * The table separator to be used between tables.
	 * @var string
	 */
	protected $tableSeparator;

	/**
	 * Construct the Joins object.
	 *
	 * @param Evoke\Persistence\DB\Table\InfoIface
	 *                    DB Table Info object.
	 * @param string      Table Name.
	 * @param string      Parent Field.
	 * @param string      Child Field.
	 * @param Evoke\Persistence\DB\Table\JoinsIface[]
	 *                    Joins from this node.
	 * @param string      Admin Managed.
	 * @param string      Compare Type.
	 * @param string      ID Separator.
	 * @param string      Join Type.
	 * @param string      Joint Key.
	 * @param string|null Table Alias.
	 * @param string      Table Separator.
	 */
	public function __construct(
		InfoIface    $info,
		/* String */ $tableName,
		/* String */ $parentField    = NULL,
		/* String */ $childField     = NULL,
		Array        $joins          = array(),
		/* Bool   */ $adminManaged   = true,
		Array        $autoFields     = array('ID'),
		/* String */ $compareType    = '=',
		/* String */ $idSeparator    = '_',
		/* String */ $joinType       = 'LEFT JOIN',
		/* String */ $jointKey       = 'Joint_Data',
		/* Mixed  */ $tableAlias     = NULL,
		/* String */ $tableSeparator = '_T_')
	{
		if (!is_string($tableName))
		{
			throw new InvalidArgumentException(
				__METHOD__ . ' requires tableName as string');
		}
		
		$this->adminManaged   = $adminManaged;
		$this->autoFields     = $autoFields;
		$this->childField     = $childField;
		$this->compareType    = $compareType;
		$this->idSeparator    = $idSeparator;
		$this->info           = $info;
		$this->joinType       = $joinType;
		$this->joins          = $joins;
		$this->jointKey       = $jointKey;
		$this->parentField    = $parentField;
		$this->tableAlias     = $tableAlias;
		$this->tableName      = $tableName;
		$this->tableSeparator = $tableSeparator;
	}
   
	/******************/
	/* Public Methods */
	/******************/

	/**
	 * Arrange a set of results for the database that match the Join tree.
	 *
	 * @param mixed[] The results from the database.
	 * @param mixed[] The data already processed from the results.
	 *
	 * @return mixed[] The data that was arranged from the results.
	 */
	public function arrangeResults(Array $results, Array $data=array())
	{
		// Get the data from the current table (this is a recursive function).
		foreach ($results as $rowResult)
		{
			$currentTableResult = $this->filterFields($rowResult);
	 
			// Determine whether the row has data for the current table.
			if ($this->isResult($currentTableResult))
			{
				$rowID = $this->getRowID($currentTableResult);
	    
				if (!isset($data[$rowID]))
				{
					$data[$rowID] = $currentTableResult;
				}

				// If this result could contain information for referenced tables
				// lower in the heirachy set it in the joint data.
				if (!empty($this->joins))
				{
					if (!isset($data[$rowID][$this->jointKey]))
					{
						$data[$rowID][$this->jointKey] = array();
					}

					$jointData = &$data[$rowID][$this->jointKey];

					// Fill in the data for the joins by recursion.
					foreach($this->joins as $ref)
					{
						$jointDataField = $ref->getParentField();
		  
						if (!isset($jointData[$jointDataField]))
						{
							$jointData[$jointDataField] = array();
						}

						// Recurse - Arrange the single result (rowResult).
						$jointData[$jointDataField]  = $ref->arrangeResults(
							array($rowResult), $jointData[$jointDataField]);
					}	  
				}
			}
		}

		return $data;
	}
   
	/**
	 * Get a list of all fields fully named by their table alias.
	 *
	 * @return mixed[]
	 */
	public function getAllFields()
	{
		$fields = array();
		$tableFields = $this->info->getFields();

		foreach ($tableFields as $field)
		{
			$fields[] = $this->getTableAlias() . '.' . $field . ' AS ' .
				$this->getTableAlias() . $this->tableSeparator . $field;
		}
      
		if (!empty($this->joins))
		{
			foreach ($this->joins as $ref)
			{
				$fields = array_merge($fields, $ref->getAllFields());
			}
		}

		return $fields;
	}

	/**
	 * Get the fields that should be left for auto filling.
	 *
	 * @return mixed[]
	 */
	public function getAutoFields()
	{
		return $this->autoFields;
	}
   
	/**
	 * Get the child field.
	 *
	 * @return string
	 */
	public function getChildField()
	{
		return $this->childField;
	}
   
	/**
	 * Get the compare type.
	 *
	 * @return string
	 */
	public function getCompareType()
	{
		return $this->compareType;
	}

	/**
	 * Get an empty joint data record.
	 *
	 * @return mixed[]
	 */
	public function getEmpty()
	{
		$emptyRecord = array();

		foreach ($this->joins as $ref)
		{
			$emptyRecord[$this->jointKey][$ref->getParentField()]
				= $ref->getEmpty();
		}

		return $emptyRecord;
	}

	/**
	 * Return any failures from validation of data.
	 *
	 * @return Evoke\Message\TreeIface
	 */
	public function getFailures()
	{
		return $this->info->getFailures();
	}
	
	/**
	 * Get the joins.
	 *
	 * @return Evoke\Persistence\DB\Table\JoinsIface[]
	 */
	public function getJoins()
	{
		return $this->joins;
	}
      
	/**
	 * Get the join statement for the tables.
	 *
	 * @return string
	 */
	public function getJoinStatement()
	{
		$joinStatement = '';

		if (!empty($this->joins))
		{
			foreach ($this->joins as $ref)
			{
				$joinStatement .= $this->buildJoin($ref) . $ref->getJoinStatement();
			}
		}

		return $joinStatement;
	}

	/**
	 * Get the join type.
	 *
	 * @return string
	 */
	public function getJoinType()
	{
		return $this->joinType;
	}
   
	/**
	 * Get the Joint_Key.
	 *
	 * @return string
	 */
	public function getJointKey()
	{
		return $this->jointKey;
	}

	/**
	 * Get the parent field.
	 *
	 * @return string
	 */
	public function getParentField()
	{
		return $this->parentField;
	}

	/**
	 * Get the primary keys for the table (not all primary keys for all
	 * referenced tables).
	 *
	 * @return string
	 */
	public function getPrimaryKeys()
	{
		return $this->info->getPrimaryKeys();
	}
   
	/**
	 * Get the table name that has possibly been aliassed.
	 *
	 * @return string
	 */
	public function getTableAlias()
	{
		if (isset($this->tableAlias))
		{
			return $this->tableAlias;
		}
		else
		{
			return $this->tableName;
		}
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
	 * Get the table separator.
	 *
	 * @return string
	 */
	public function getTableSeparator()
	{
		return $this->tableSeparator;
	}
   
	/**
	 * Whether the table reference is administratively managed.
	 *
	 * @return bool
	 */
	public function isAdminManaged()
	{
		return $this->adminManaged;
	}
   
	/**
	 * Whether the table has an alias that is set.
	 *
	 * @return bool
	 */
	public function isTableAliassed()
	{
		return isset($this->tableAlias);
	}

	/**
	 * Whether the fieldset is valid.
	 *
	 * @param mixed[] The fieldset.
	 * @param mixed[] Fields that should be ignored in the fieldset.
	 *
	 * @return bool
	 */
	public function isValid($fieldset, $ignoredFields=array())
	{
		return $this->info->isValid($fieldset, $ignoredFields);
	}

	/*********************/
	/* Protected Methods */
	/*********************/

	/**
	 * Get a row ID value that uniquely identifies a row for a table.
	 *
	 * @param mixed[] The row from the result.
	 *
	 * @return string
	 */
	protected function getRowID($row)
	{
		$id = NULL;
		$primaryKeys = $this->info->getPrimaryKeys();

		foreach ($primaryKeys as $primaryKey)
		{
			if (isset($row[$primaryKey]))
			{
				if (empty($id))
				{
					$id = $row[$primaryKey];
				}
				else
				{
					$id .= $this->idSeparator . $row[$primaryKey];
				}
			}
		}
      
		return $id;
	}
      
   /*******************/
   /* Private Methods */
   /*******************/
   
	/**
	 * Build the join from the join components.
	 *
	 * @param Evoke\Persistence\DB\Table\JoinsIface
	 *
	 * @return string
	 */
	private function buildJoin($ref)
	{
		$join = ' ' . $ref->getJoinType() . ' ' . $ref->getTableName();

		if ($ref->isTableAliassed())
		{
			$join .= ' AS ' . $ref->getTableAlias();
		}
      
		return $join . ' ON ' . $this->getTableAlias() . '.' .
			$ref->getParentField() . $ref->getCompareType() .
			$ref->getTableAlias() . '.' .  $ref->getChildField();
	}

	/**
	 * Filter the fields that belong to a table from the list and return the
	 * fields without their table name preceding them.
	 *
	 * @param mixed[] The field list that we are filtering.
	 *
	 * @return mixed[]
	 */
	private function filterFields($fieldList)
	{
		$filteredFields = array();
		$pattern =
			'/^' . $this->getTableAlias() . $this->tableSeparator . '/';

		foreach ($fieldList as $key => $value)
		{
			if (preg_match($pattern, $key))
			{
				$filteredFields[preg_replace($pattern, '', $key)] = $value;
			}
		}

		return $filteredFields;
	}

	/**
	 * Determine whether the result is a result (has data for this table).
	 *
	 * @param result The result data.
	 *
	 * @return bool Whether the result contains information for this table.
	 */
	private function isResult($result)
	{
		// A non result may be an array with all NULL entries, so we cannot just
		// check that the result array is empty.  The easiest way is just to check
		// that there is at least one value that is set.
		foreach ($result as $resultData)
		{
			if (isset($resultData))
			{
				return true;
			}
		}
      
		return false;
	}
}
// EOF