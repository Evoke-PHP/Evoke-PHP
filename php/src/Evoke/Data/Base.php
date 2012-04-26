<?php
namespace Evoke\Data;
/** Provide access to data.  Related data is handled through the Joins. An
 *  iterator is supplied to traverse the array of records that make up the data.
 *  Fields from the array can be accessed as per standard Array access.  Whilst
 *  Joint_Data is retrieved via class properties that are automatically created
 *  from the Joins passed at construction.
 *
 *  Below is a usage example containing each different type of access:
 *  @code
 *  $obj = new Data();
 *  $obj->setData($data);
 *
 *  // Traverse over each record in the data.
 *  foreach ($obj as $key => $record)
 *  {
 *     // Access a field as though it is an array.
 *     $x = $record['Field'];
 *
 *     // Access joint data (with ->).  The joint data is itself a data object.
 *     foreach ($record->list as $listRecord)
 *     {
 *        $y = $listRecord['Joint_Record_Field'];
 *     }
 *  }
 *  @endcode
*/
class Base implements \Evoke\Iface\Data
{
	/** @property $collisionFreeSetup
	 *  Due to the way data is accessed from the Data class the number of
	 *  properties within the class must be limited to avoid collisions. The name
	 *  of our one property is also designed to avoid collisions.  This is due to
	 *  the implementation of the @ref __get method which provides access to
	 *  joint data.
	 */
	protected $collisionFreeSetup;
		
	public function __construct(Array        $joins,
	                            Array        $data     = array(),
	                            /* String */ $jointKey = 'Joint_Data')
	{
		if (!is_array($joins))
		{
			throw new \InvalidArgumentException(
				__METHOD__ . ' requires Joins as array');
		}
		
		foreach ($joins as $parentField => $dataContainer)
		{
			if (!$dataContainer instanceof \Evoke\Iface\Data)
			{
				throw new \InvalidArgumentException(
					__METHOD__ . ' requires Data for parent field: ' .
					$parentField);
			}
		}

		$this->collisionFreeSetup = array('Data'      => $data,
		                                  'Joins'     => $joins,
		                                  'Joint_Key' => $jointKey);
	}

	/******************/
	/* Public Methods */
	/******************/

	/** Provide access to the joint data.  This allows the object to be used like
	 *  so:  $object->referencedData (for joint data with a parent field of
	 *  'Referenced_Data').
	 *  @param parentField @string The parent field for the joint data.
	 *  This can be as per the return value of @ref getJoinName.
	 */
	public function __get($parentField)
	{
		if (isset($this->collisionFreeSetup['Joins'][$parentField]))
		{
			return $this->collisionFreeSetup['Joins'][$parentField];
		}
      
		foreach ($this->collisionFreeSetup['Joins'] as $pField => $dataContainer)
		{
			if ($parentField === $this->getJoinName($pField))
			{
				return $dataContainer;
			}
		}
      
		throw new \OutOfBoundsException(
			__METHOD__ . ' record does not refer to: ' .
			var_export($parentField, true) . ' joins are: ' .
			var_export($this->collisionFreeSetup['Joins'], true));
	}
	
	/** Get the current record as a simple array (without iterator or class
	 *  properties).
	 *  @return Array The record that we are managing.
	 */
	public function getRecord()
	{
		return current($this->collisionFreeSetup['Data']);
	}
	
	/** Return whether the data is empty or not.
	 *  @return @bool Whether the data is empty or not.
	 */
	public function isEmpty()
	{
		return empty($this->collisionFreeSetup['Data']);
	}
   
	/** Set the data that we are managing.
	 *  @param @array The data we want to manage.
	 */
	public function setData(Array $data)
	{
		$this->collisionFreeSetup['Data'] = $data;
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
		return key($this->collisionFreeSetup['Data']);
	}

	/** Get the next record of data. Set the next record within the Data object
	 *  and return the object.
	 */
	public function next()
	{
		$nextItem = next($this->collisionFreeSetup['Data']);

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
		$first = reset($this->collisionFreeSetup['Data']);

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
		return (current($this->collisionFreeSetup['Data']) !== false);
	}

	/**************************/
	/* Implements ArrayAccess */
	/**************************/
   
	/// Provide the array isset operator.
	public function offsetExists($offset)
	{
		$record = current($this->collisionFreeSetup['Data']);
		return isset($record[$offset]);
	}

	/// Provide the array access operator.
	public function offsetGet($offset)
	{
		$record = current($this->collisionFreeSetup['Data']);
		return $record[$offset];
	}

	/** We are required to make these available to complete the interface,
	 *  but we don't want the element to change, so this should never be called.
	 *  @return Throws an exception.
	 */
	public function offsetSet($offset, $value)
	{
		throw new \RuntimeException(
			__METHOD__ . ' should never be called - data is only transferrable ' .
			'it is not to be modified.');
	}

	/** We are required to make these available to complete the interface,
	 *  but we don't want the element to change, so this should never be called.
	 *  @return Throws an exception.
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

	/** Get the collision free setup so that inherited classes don't have to
	 *  refer to $this->collisionFreeSetup.
	 *  @return @array The value of $this->collisionFreeSetup
	 */
	protected function getSetup()
	{
		return $this->collisionFreeSetup;
	}
	
	/** Set all of the Joint Data from the current record into the data
	 *  containers supplied by the references given at construction.
	 */
	protected function setRecord($record)
	{
		foreach ($this->collisionFreeSetup['Joins'] as $parentField => $data)
		{
			if (isset($record[
				          $this->collisionFreeSetup['Joint_Key']][$parentField]))
			{
				$data->setData(
					$record[$this->collisionFreeSetup['Joint_Key']][$parentField]);
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