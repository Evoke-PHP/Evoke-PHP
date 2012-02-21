<?php
namespace Evoke\Core\DB\Table;
/** The Joins class is used to interact with Relational Databases.
 *  Relational databases have tables of data that are linked to other tables
 *  via Foreign Keys.  By understanding the relationships between data we can
 *  deal with data in real world units.
 *
 *  To match the relational model in code we need to represent a Tree of
 *  relations from the table of data.  The Joins object is thus a node in a Tree
 *  of Joins. Recursive methods to process the tree of joins enable the data to
 *  be joint together and used. 
 *
 *  Example:
 *     List of products, each of a particular size with a set of related images.
 *  
 *  SQL structure:
 *     Product:    ID, Name, Image_List_ID, Size_ID
 *                 (Foreign Keys: Image_List_ID -> Image_List.List_ID,
 *                                Size_ID       -> Size.ID)
 *     Image_List: ID, List_ID, Image_ID
                   (Foreign Keys: Image_ID -> Image.ID)
 *     Image:      ID, Filename
 *     Size:       ID, Name
 *
 *  Joins:
 *     Image_List_ID -> Child_Field => List_ID,
 *                      Table_Name  => Image_List,
 *                      Joins       => 
 *                         Image_ID -> Child_Field => ID,
 *                                     Table_Name  => Image,
 *     Size_ID       -> Child_Field => ID,
 *                      Table_Name  => Size
 *
 *  The above is an abstract representation of the Joins tree that would
 *  represent the data.  The Joins class models the above with its properties
 *  and Joins array which contains references to further Joins objects.  The
 *  methods within the class are used to process the joins tree.
 */
class Joins
{
	/** @property $setup
	 *  The setup for the Joins data.
	 */
	protected $setup;
   
	public function __construct(Array $setup=array())
	{
		$this->setup = array_merge(
			array('Admin_Managed'   => true,
			      'Auto_Fields'     => array('ID'),
			      'Child_Field'     => NULL,
			      'Compare_Type'    => '=',
			      'ID_Separator'    => '_',
			      'Join_Type'       => 'LEFT JOIN',
			      'Joins'           => array(),
			      'Joint_Key'       => 'Joint_Data',
			      'Parent_Field'    => NULL,
			      'Table_Alias'     => NULL,
			      'Table_Info'       => NULL,
			      'Table_Name'      => NULL,
			      'Table_Separator' => '_T_'),
			$setup);

		if (!$this->setup['Table_Info'] instanceof Info)
		{
			throw new \InvalidArgumentException(
				__METHOD__ . ' requires TableInfo');
		}
      
		if (!isset($this->tableName))
		{
			throw new \InvalidArgumentException(
				__METHOD__ . ' needs Table_Name');
		}
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
				if (!empty($this->setup['Joins']))
				{
					if (!isset($data[$rowID][$this->jointKey]))
					{
						$data[$rowID][$this->jointKey] = array();
					}

					$jointData = &$data[$rowID][$this->jointKey];

					// Fill in the data for the joins by recursion.
					foreach($this->setup['Joins'] as $ref)
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
		$tableFields = $this->setup['Table_Info']->getFields();

		foreach ($tableFields as $field)
		{
			$fields[] = $this->getTableAlias() . '.' . $field . ' AS ' .
				$this->getTableAlias() . $this->tableSeparator . $field;
		}
      
		if (!empty($this->setup['Joins']))
		{
			foreach ($this->setup['Joins'] as $ref)
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

		foreach ($this->setup['Joins'] as $ref)
		{
			$emptyRecord[$this->jointKey][$ref->getParentField()]
				= $ref->getEmpty();
		}

		return $emptyRecord;
	}

	/// Return any failures from validation of data.
	public function getFailures()
	{
		return $this->setup['Table_Info']->getFailures();
	}
	
	/// Get the joins.
	public function getJoins()
	{
		return $this->setup['Joins'];
	}
      
	/// Get the join statement for the tables.
	public function getJoinStatement()
	{
		$joinStatement = '';

		if (!empty($this->setup['Joins']))
		{
			foreach ($this->setup['Joins'] as $ref)
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
		return $this->setup['Table_Info']->getPrimaryKeys();
	}
   
	// Get a row ID that uniquely identifies a row for a table.
	protected function getRowID($row)
	{
		$id = NULL;
		$primaryKeys = $this->setup['Table_Info']->getPrimaryKeys();

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
					$id .= $this->setup['ID_Separator'] . $row[$primaryKey];
				}
			}
		}
      
		return $id;
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
		return $this->setup['Table_Info']->isValid($fieldset, $ignoredFields);
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