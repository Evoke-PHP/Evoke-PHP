<?php
namespace Evoke\Model\Data;

use InvalidArgumentException,
	OutOfBoundsException,
	RuntimeException;

/**
 * Data
 *
 * Provide access to data.  Related data is allowed by defining Joins. An
 * iterator is supplied to traverse the array of records that make up the data.
 * Fields from the array can be accessed as per standard Array access.  Whilst
 * Joint Data is retrieved via class properties that are automatically created
 * from the Joins passed at construction.
 *
 * Below is a usage example containing each different type of access:
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