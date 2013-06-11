<?php
namespace Evoke\Model\Data;

use InvalidArgumentException,
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
class Data extends DataAbstract
{
	/**
	 * Joint data objects.
	 * @var DataIface[]
	 */
	protected $dataJoins;

	/**
	 * The key that is used for joint data within the raw data.
	 * @var string
	 */
	protected $jointKey;

	/**
	 * Construct a Data model.
	 *
	 * @param Array[]     $data      Raw joint data that we are modelling.
	 * @param DataIface[] $dataJoins Data objects to use for modelling the data
	 *                               that is joint with this data.
	 * @param string      $jointKey  The key to use for joint data.
	 */
	public function __construct(Array        $data      = array(),
	                            Array        $dataJoins = array(),
	                            /* String */ $jointKey  = 'Joint_Data')
	{
		if (!is_string($jointKey))
		{
			throw new InvalidArgumentException(
				'jointKey can only be a string.');
		}
		
		foreach ($dataJoins as $parentField => $dataContainer)
		{
			if (!$dataContainer instanceof DataIface)
			{
				throw new InvalidArgumentException(
					__METHOD__ . ' requires Data for parent field: ' .
					$parentField);
			}
		}

		$this->dataJoins = $dataJoins;
		$this->jointKey  = $jointKey;

		parent::__construct($data);
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
	 * Set all of the Joint Data from the current record into the data
	 * containers supplied by the references given at construction.
	 *
	 * @param mixed[] The current record to set the joint data with.
	 */
	protected function setRecord(Array $record)
	{
		foreach ($this->dataJoins as $parentField => $data)
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
}
// EOF