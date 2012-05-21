<?php
namespace Evoke\Model;

use Evoke\Iface;

/** Provide access to data.  Related data is handled through the Joins. An
 *  iterator is supplied to traverse the array of records that make up the data.
 *  Fields from the array can be accessed as per standard Array access.  Whilst
 *  Joint_Data is retrieved via class properties that are automatically created
 *  from the Joins passed at construction.
 *
 *  Below is a usage example containing each different type of access:
 *  @code
 *  $obj = new Data(array(),
 *                  array('List_ID' => $dataObjectForList));
 *  // Setting the data of the parent sets the data for the joint lists (and
 *  // their joint lists etc.).
 *  $obj->setData($data);
 *
 *  // Traverse over each record in the data.
 *  foreach ($obj as $key => $record)
 *  {
 *     // Access a field as though it is an array.
 *     $x = $record['Field'];
 *
 *     // Access joint data (with ->).  The joint data is itself a data object.
 *     // The name used after -> is the lowerCamelCase (_ID is removed
 *     // automatically).
 *     foreach ($record->list as $listRecord)
 *     {
 *        $y = $listRecord['Joint_Record_Field'];
 *     }
 *  }
 *  @endcode
*/
class Data implements Iface\Model\Data
{
	/** @property $data
	 *  @array Data in the raw joint array.
	 */
	protected $data;

	/** @property $dataJoins
	 *  @array Array of joint data objects.
	 */
	protected $dataJoins;

	/** @property $jointKey
	 *  @string The key that is used for joint data within the raw joint data.
	 */
	protected $jointKey;

	/** Construct a Data model.
	 *  @param data      @array  Raw joint data that we are modelling.
	 *  @param dataJoins @array  Data objects to use for modelling the data that
	 *                           is joint with this data.
	 *  @param jointKey  @string The key to use for joint data.
	 */
	public function __construct(Array        $data      = array(),
	                            Array        $dataJoins = array(),
	                            /* String */ $jointKey  = 'Joint_Data')
	{
		foreach ($dataJoins as $parentField => $dataContainer)
		{
			if (!$dataContainer instanceof Iface\Model\Data)
			{
				throw new \InvalidArgumentException(
					__METHOD__ . ' requires Data for parent field: ' .
					$parentField);
			}
		}

		$this->data      = $data;
		$this->dataJoins = $dataJoins;
		$this->jointKey  = $jointKey;
		$this->rewind();
	}

	/** Provide access to the joint data as though it is a property of the
	 *  object.  For joint data with a parent field of Linked_Data the property
	 *  would be: `$object->linkedData;`.
	 *  @param parentField @string The parent field for the joint data.
	 *  This can be as per the return value of @ref getJoinName.
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
		
		throw new \OutOfBoundsException(
			__METHOD__ . ' record does not have a data container for: ' .
			var_export($parentField, true) . ' joins are: ' .
			implode(', ', array_keys($this->dataJoins)));
	}

	/******************/
	/* Public Methods */
	/******************/

	/** Get the current record as a simple array (without iterator or class
	 *  properties).
	 *  @return Array The record that we are managing.
	 */
	public function getRecord()
	{
		return current($this->data);
	}
	
	/** Return whether the data is empty or not.
	 *  @return @bool Whether the data is empty or not.
	 */
	public function isEmpty()
	{
		return empty($this->data);
	}
   
	/** Set the data that we are managing.
	 *  @param @array The data we want to manage.
	 */
	public function setData(Array $data)
	{
		$this->data = $data;
		$this->rewind();
	}   
   
	/***********************/
	/* Implements Iterator */
	/***********************/

	/** Return the current record of data (as a Data object with iterator and
	 *  reference access).  This is just the object as the object implements the
	 *  iterator and references.
	 */
	public function current()
	{
		return $this;
	}

	/// Return the key of the current data item.
	public function key()
	{
		return key($this->data);
	}

	/** Get the next record of data. Set the next record within the Data object
	 *  and return the object.
	 */
	public function next()
	{
		$nextItem = next($this->data);

		if ($nextItem === false)
		{
			return false;
		}

		$this->setRecord($nextItem);
		return $this;
	}

	/// Rewind to the first record of data.
	public function rewind()
	{
		$first = reset($this->data);

		if ($first !== false)
		{
			$this->setRecord($first);
		}
	}

	/** Return whether there are still data records to iterate over.
	 *  @return @bool Whether the current data record is valid.
	 */
	public function valid()
	{
		return (current($this->data) !== false);
	}

	/**************************/
	/* Implements ArrayAccess */
	/**************************/
   
	/// Provide the array isset operator.
	public function offsetExists($offset)
	{
		$record = current($this->data);
		return isset($record[$offset]);
	}

	/// Provide the array access operator.
	public function offsetGet($offset)
	{
		$record = current($this->data);
		return $record[$offset];
	}

	/** We are required to make these available to complete the interface,
	 *  but we don't want the element to change, so this should never be called.
	 *  @throws RuntimeException *** ALWAYS ***
	 */
	public function offsetSet($offset, $value)
	{
		throw new \RuntimeException(
			__METHOD__ . ' should never be called - data is only transferrable ' .
			'it is not to be modified.');
	}

	/** We are required to make these available to complete the interface,
	 *  but we don't want the element to change, so this should never be called.
	 *  @throws RuntimeException *** ALWAYS ***
	 */
	public function offsetUnset($offset)
	{
		throw new \RuntimeException(
			__METHOD__ . ' should never be called - data is only transferrable ' .
			'it is not to be modified.');
	}

	/*********************/
	/* Protected Methods */
	/*********************/

	/** Set all of the Joint Data from the current record into the data
	 *  containers supplied by the references given at construction.
	 */
	protected function setRecord($record)
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

	/** Get the Join name that will be used for accessing the joint data from
	 *  this object.  The joint data is a Data object and its name should match
	 *  the standard naming of our objects (lowerCamelCase) and not contain the
	 *  final ID which is not needed.
	 *  @param parentField @string The parent field for the joint data.
	 *  @return @string The reference name.
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