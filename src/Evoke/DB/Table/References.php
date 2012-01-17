<?php
namespace Evoke\DB;
/// Represent table references used in joins.
class Table_References
{
   /// The table reference data.
   protected $setup;
   
   public function __construct($setup=array())
   {
      $this->setup = array_merge(
	 array('Admin_Managed'   => true,
	       'Auto_Fields'     => array('ID'),
	       'Callbacks'       => array(),
	       'Child_Field'     => NULL,
	       'Compare_Type'    => '=',
	       'ID_Separator'    => '_',
	       'Join_Type'       => 'LEFT JOIN',
	       'Joint_Key'       => 'Joint_Data',
	       'Parent_Field'    => NULL,
	       'References'      => array(),
	       'Table_Alias'     => NULL,
	       'Table_Info'      => NULL,
	       'Table_Name'      => NULL,
	       'Table_Separator' => '_T_'),
	 $setup);

      if (!isset($this->setup['Table_Name']))
      {
	 throw new \InvalidArgumentException(
	    __METHOD__ . ' needs Table_Name');
      }
   }
   
   /******************/
   /* Public Methods */
   /******************/

   /// Arrange a set of results for the table reference structure.
   public function arrangeResults($results, $data=array())
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
	    if (!empty($this->setup['References']))
	    {
	       if (!isset($data[$rowID][$this->setup['Joint_Key']]))
	       {
		  $data[$rowID][$this->setup['Joint_Key']] = array();
	       }

	       $jointData = &$data[$rowID][$this->setup['Joint_Key']];

	       // Fill in the data for the references by recursion.
	       foreach($this->setup['References'] as $ref)
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

   /// Run the callback for extra processing.
   public function callback($function, $data)
   {
      if (isset($this->setup['Callbacks'][$function]))
      {
	 call_user_func_array($this->setup['Callbacks'][$function],
			      array($data));
      }
   }
   
   /// Get a list of all fields fully named by their table alias.
   public function getAllFields()
   {
      $fields = array();
      $tableFields = $this->setup['Table_Info']->getFields();

      foreach ($tableFields as $field)
      {
	 $fields[] = $this->getTableAlias() . '.' . $field . ' AS ' .
	    $this->getTableAlias() . $this->setup['Table_Separator'] . $field;
      }
      
      if (!empty($this->setup['References']))
      {
	 foreach ($this->setup['References'] as $ref)
	 {
	    $fields = array_merge($fields, $ref->getAllFields());
	 }
      }

      return $fields;
   }

   /// Get the fields that should be left for auto filling.
   public function getAutoFields()
   {
      return $this->setup['Auto_Fields'];
   }
   
   /// Get the child field.
   public function getChildField()
   {
      return $this->setup['Child_Field'];
   }
   
   /// Get the compare type.
   public function getCompareType()
   {
      return $this->setup['Compare_Type'];
   }

   /// Get an empty joint data record.
   public function getEmpty()
   {
      $emptyRecord = array();

      foreach ($this->setup['References'] as $ref)
      {
	 $emptyRecord[$this->setup['Joint_Key']][$ref->getParentField()]
	    = $ref->getEmpty();
      }

      return $emptyRecord;
   }

   /// Return any failures from validation of data.
   public function getFailures()
   {
      return $this->setup['Table_Info']->getFailures();
   }
   
   /// Get the join statement for the tables.
   public function getJoins()
   {
      $joinStatement = '';

      if (!empty($this->setup['References']))
      {
	 foreach ($this->setup['References'] as $ref)
	 {
	    $joinStatement .= $this->buildJoin($ref) . $ref->getJoins();
	 }
      }

      return $joinStatement;
   }

   /// Get the join type.
   public function getJoinType()
   {
      return $this->setup['Join_Type'];
   }
   
   // Get the Joint_Key.
   public function getJointKey()
   {
      return $this->setup['Joint_Key'];
   }

   /// Get the parent field.
   public function getParentField()
   {
      return $this->setup['Parent_Field'];
   }

   /// Get the primary keys for the table (not all primary keys for all referenced tables).
   public function getPrimaryKeys()
   {
      return $this->setup['Table_Info']->getPrimaryKeys();
   }
   
   /// Get the references.
   public function getReferences()
   {
      return $this->setup['References'];
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
      if (isset($this->setup['Table_Alias']))
      {
	 return $this->setup['Table_Alias'];
      }
      else
      {
	 return $this->setup['Table_Name'];
      }
   }

   /// Get the table name.
   public function getTableName()
   {
      return $this->setup['Table_Name'];
   }

   /// Get the table separator.
   public function getTableSeparator()
   {
      return $this->setup['Table_Separator'];
   }
   
   /// Whether the table reference is administratively managed.
   public function isAdminManaged()
   {
      return $this->setup['Admin_Managed'];
   }
   
   /// Return whether the table has an alias that is set.
   public function isTableAliassed()
   {
      return isset($this->setup['Table_Alias']);
   }

   public function isValid($fieldset, $ignoredFields=array())
   {
      return $this->setup['Table_Info']->isValid($fieldset, $ignoredFields);
   }
   
   /*********************/
   /* Protected Methods */
   /*********************/
   
   /// Build the join from the join components.
   protected function buildJoin($ref)
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
   protected function filterFields($fieldList)
   {
      $filteredFields = array();
      $pattern =
	 '/^' . $this->getTableAlias() . $this->setup['Table_Separator'] . '/';

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
   protected function isResult($result)
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