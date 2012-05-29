<?php
namespace Evoke\DB\Table;

use Evoke\Iface;

/** The JoinTree class is used to interact with Relational Databases.
 *  Relational databases have tables of data that are linked to other tables
 *  via Foreign Keys.  The JoinTree provides a way or representing these
 *  relationships so that data can be managed in real world units (with related
 *  data joint by the appropriate relationship).
 *
 *  Example:
 *     List of products, each of a particular size with a set of related images.
 *  
 *  SQL structure (PK = Primary Key, FK = Foreign Key):
 *  @verbatim
 *    +====================+     
 *    | Product            |     +===============+
 *    +--------------------+     | Image_List    |
 *    | PK | ID            |     +---------------+     +===============+
 *    |    | Name          |     | PK | ID       |     | Image         |
 *    | FK | Image_List_ID |---->|    | List_ID  |     +---------------+
 *    | FK | Size_ID       |-.   | FK | Image_ID |---->| PK | ID       |
 *    +====================+ |   +===============+     |    | Filename |
 *                           |                         +===============+
 *                           |   +===========+
 *                    	     |   | Size      |
 *                    	     |   +-----------+
 *                    	     `-->| PK | ID   |
 *                    	         |    | Name |
 *                    	         +===========+
 *  @endverbatim
 *
 *  Joins:
 *  @verbatim
 *  array(Table_Name => Product,
 *        Joins => array(
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
 *  @endverbatim
 *
 *  The above is an abstract representation of the Joins tree that would
 *  represent the data.  The JoinTree class models the above with its properties
 *  and Joins array which contains references to further JoinTree objects.  The
 *  methods within the class are used to process the JoinTree.
 */
class Joins implements Iface\DB\Table\Joins
{
	/** @property $adminManaged
	 *  \bool Whether the current join is administatively managed (for the
	 *  purposes of adding, editing or deleting data).
	 */
	protected $adminManaged;

	/** @property $autoFields
	 *  Fields \array that are handled automatically by the database.
	 */
	protected $autoFields;

	/** @property $childField
	 *  The child field \string of the join.  This is the name of the field in
	 *  the table that this join points to.
	 */
	protected $childField;

	/** @property $compareType
	 *  \string How a match should be determined between the parent field and
	 *  child field.
	 */
	protected $compareType;

	/** @property $idSeparator
	 *  \string Separator string to use between IDs in a table with multiple
	 *  keys.
	 */
	protected $idSeparator;

	/** @property $info
	 *  Database Table Info \object
	 */
	protected $info;
	
	/** @property $joinType
	 *  \string The type of the join ('LEFT JOIN', 'RIGHT JOIN') etc.
	 */
	protected $joinType;

	/** @property $joins
	 *  \array of Join objects from this node to other Join objects in the join
	 *  tree.
	 */
	protected $joins;

	/** @property $jointKey
	 *  \string The field used to join records together.
	 */
	protected $jointKey;

	/** @property $parentField
	 *  The parent field for the current join.  The parent field is related to
	 *  the Join object that is the current node's parent.  It is the field in
	 *  that node's Table.
	 */
	protected $parentField;

	/** @property $tableAlias
	 *  The table name \string to be used for the current table (Aliases can be
	 *  used to disambiguate data).
	 */
	protected $tableAlias;

	/** @property $tableName
	 *  The table name \string of the current table.
	 */
	protected $tableName;

	/** @property $tableSeparator
	 *  The table separator \string to be used between tables.
	 */
	protected $tableSeparator;

	/** Construct the Joins object.
	 *  @param joins        @array  Joins from this node.
	 *  @param info         @object DB Table Info object.
	 *
	 */
	public function __construct(
		Iface\DB\Table\Info $info,
		/* String */        $tableName,
		/* String */        $parentField    = NULL,
		/* String */        $childField     = NULL,
		Array               $joins          = array(),
		/* Bool   */        $adminManaged   = true,
		Array               $autoFields     = array('ID'),
		/* String */        $compareType    = '=',
		/* String */        $idSeparator    = '_',
		/* String */        $joinType       = 'LEFT JOIN',
		/* String */        $jointKey       = 'Joint_Data',
		/* Mixed  */        $tableAlias     = NULL,
		/* String */        $tableSeparator = '_T_')
	{
		if (!is_string($tableName))
		{
			throw new \InvalidArgumentException(
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

	/** Arrange a set of results for the database that match the Join tree.
	 *  @param results \array The results from the database.
	 *  @param data \array The data already processed from the results.
	 *  \return \array The data that was arranged from the results.
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
   
	/// Get a list of all fields fully named by their table alias.
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

	/// Get the fields that should be left for auto filling.
	public function getAutoFields()
	{
		return $this->autoFields;
	}
   
	/// Get the child field.
	public function getChildField()
	{
		return $this->childField;
	}
   
	/// Get the compare type.
	public function getCompareType()
	{
		return $this->compareType;
	}

	/// Get an empty joint data record.
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

	/// Return any failures from validation of data.
	public function getFailures()
	{
		return $this->info->getFailures();
	}
	
	/// Get the joins.
	public function getJoins()
	{
		return $this->joins;
	}
      
	/// Get the join statement for the tables.
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

	/// Get the join type.
	public function getJoinType()
	{
		return $this->joinType;
	}
   
	// Get the Joint_Key.
	public function getJointKey()
	{
		return $this->jointKey;
	}

	/// Get the parent field.
	public function getParentField()
	{
		return $this->parentField;
	}

	/// Get the primary keys for the table (not all primary keys for all referenced tables).
	public function getPrimaryKeys()
	{
		return $this->info->getPrimaryKeys();
	}
   
	/// Get the table name that has possibly been aliassed.
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

	/// Get the table name.
	public function getTableName()
	{
		return $this->tableName;
	}

	/// Get the table separator.
	public function getTableSeparator()
	{
		return $this->tableSeparator;
	}
   
	/// Whether the table reference is administratively managed.
	public function isAdminManaged()
	{
		return $this->adminManaged;
	}
   
	/// Return whether the table has an alias that is set.
	public function isTableAliassed()
	{
		return isset($this->tableAlias);
	}

	public function isValid($fieldset, $ignoredFields=array())
	{
		return $this->info->isValid($fieldset, $ignoredFields);
	}

	/*********************/
	/* Protected Methods */
	/*********************/

	// Get a row ID that uniquely identifies a row for a table.
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
   
	/// Build the join from the join components.
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

	/** Filter the fields that belong to a table from the list and return the
	 *  fields without their table name preceding them.
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

	/// Determine whether the result is a result (has data for this table).
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