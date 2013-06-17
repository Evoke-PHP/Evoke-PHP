<?php
namespace Evoke\Model\Data;

use Evoke\Model\Data\Metadata\MetadataIface,
	InvalidArgumentException,
	OutOfBoundsException,
	RuntimeException;

/**
 * Data
 * ====
 *
 * Overview
 * --------
 *
 * Provide access to data.  Related data is allowed by defining Joins. An
 * iterator is supplied to traverse the array of records that make up the data.
 * Fields from the array can be accessed as per standard Array access.  Whilst
 * Joint Data is retrieved via class properties that are automatically created
 * from the Joins passed at construction.
 *
 * Joins
 * -----
 *
 * Joins provide a way of representing trees of data commonly found in
 * relational databases and many other real world situations.  They allow us to
 * work with a hierarchy of information considering each part in the hierarchy
 * as a separate data unit.
 *
 * Example from a relational database:
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
 * array(Joins => array(
 *	          array(Child_Field  => List_ID,
 *	                Parent_Field => Image_List_ID,
 *	                Table_Name   => Image_List,
 *	                Joins        => array(
 *	                    array(Child_Field => ID,
 *	                          Parent_Field => Image_ID,
 *	                          Table_Name  => Image))),
 *	          array(Child_Field  => ID,
 *	                Parent_Field => Size_ID,
 *	                Table_Name   => Size)),
 *       Table_Name => Product);
 * </code></pre>
 *
 * The above is an abstract representation of the joins that would
 * represent the data.
 *
 * Results
 * -------
 *
 * Result records commonly come as flat records. To interpret these we need
 * to arrange them according to the hierarchy of joins that represents our data
 * structure.
 *
 * Usage
 * -----
 *
 * 	   $data = new Data($dataFromMapperOrOtherSource,
 * 	                   array('List_ID' => $dataObjectForList));
 *
 * 	   // Traverse over each record in the data.
 * 	   foreach ($data as $key => $record)
 * 	   {
 * 	       // Access a field as though it is an array.
 * 	       $x = $record['Field'];
 *
 * 	       // Access joint data (with ->).  The joint data is itself a data
 *         // object.  The name used after -> is the lowerCamelCase (_ID is
 *         // removed automatically).
 * 	       foreach ($record->list as $listRecord)
 * 	       {
 * 	           $y = $listRecord['Joint_Record_Field'];
 * 	       }
 *     }
 * 
 * @author Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license MIT
 * @package Model
 */
class Data
{
	protected
		/**
		 * Description of the data we are modelling.
		 * @var MetadataIface
		 */
		$metadata;

	/**
	 * Construct a Data model.
	 *
	 * @param MetadataIface Description of the data we are modelling.
	 * @param Array[]       Raw data that we are modelling.
	 */
	public function __construct(MetadataIface $metadata,
	                            Array         $data = array())
	{
		parent::__construct($data);
		
		$this->metadata = $metadata;
	}

	/**
	 * Provide access to the joint data as though it is a property of the
	 * object.
	 *
	 * For joint data with a parent field of Link_ID the property would be
	 * either:
	 *     $object->linkId;
	 *     // OR
	 *     $object->link;
	 *
	 * This is because we convert the name from Upper_Pascal_Case and optionally
	 * remove 'ID' from the end of the parent field.
	 *
	 * @param string The parent field for the joint data.  This can be as per
	 *               the return value of getJoinName.
	 */
	public function __get($parentField)
	{
		return $this->metadata->getJointData($parentField);
		
		if (isset($this->dataJoins[$parentField]))
		{
			return $this->dataJoins[$parentField];
		}
      
		foreach ($this->dataJoins as $pField => $dataContainer)
		{
			if ($parentField === $this->getJoinName($pField))
			{
				return $dataContainer;
			}
		}
		
		throw new OutOfBoundsException(
			__METHOD__ . ' record does not have a data container for: ' .
			var_export($parentField, true) . ' joins are: ' .
			implode(', ', array_keys($this->dataJoins)));
	}

	/**
	 * Arrange a set of results for the database according to the Join tree.
	 *
	 * @param mixed[] The flat result data.
	 * @param mixed[] The data already processed from the results.
	 *
	 * @returns mixed[] The data arranged into a hierarchy by the joins.
	 */
	public function arrangeFlatData(Array $results, Array $data=array())
	{
		foreach ($results as $row)
		{
			$currentLevelResult = $this->filterFields($row);

			$data[] = $currentLevelResult;
		}
		
		return $data;
		
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

				// If this result could contain information for referenced
				// tables lower in the heirachy set it in the joint data.
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

	/**
	 * Set all of the Joint Data from the current record into the data
	 * containers supplied by the references given at construction.
	 *
	 * @param mixed[] The current record to set the joint data with.
	 */
	protected function setRecord(Array $record)
	{
		foreach ($this->dataJoins['Joins'] as $parentField => $data)
		{
			if (isset($record[$this->jointKey][$parentField]))
			{
				$data->setData($record[$this->jointKey][$parentField]);
			}
			else
			{
				$data->setData(array());
			}
		}
	}

	/*******************/
	/* Private Methods */
	/*******************/

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
		$filteredFields = [];
		$pattern = '/^' . $this->dataJoins['Table_Name'] .
			$this->dataJoint['Table_Separator'] . '/';
		
		foreach ($fieldList as $key => $value)
		{
			if (preg_match($pattern, $key))
			{
				$filteredFields[preg_replace($pattern, '', $key)] = $value;
			}
		}

		return $filteredFields;
		
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
	 * Get the Join name that will be used for accessing the joint data from
	 * this object.  The joint data is a Data object and its name should match
	 * the standard naming of our objects (lowerCamelCase) and not contain the
	 * final ID which is not needed.
	 *
	 * @param string The parent field for the joint data.
	 *
	 * @return string The reference name.
	 */
	private function getJoinName($parentField)
	{
		$nameParts = explode('_', $parentField);
		$lastPart = end($nameParts);

		// Remove any final id.
		if (strtolower($lastPart) === 'id')
		{
			array_pop($nameParts);
		}

		$name = '';

		foreach ($nameParts as $part)
		{
			$name .= ucfirst($part);
		}

		return lcfirst($name);
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
		// check that the result array is empty.  The easiest way is just to
		// check that there is at least one value that is set.
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