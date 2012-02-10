<?php
namespace Evoke\Data;
/** Provide access to data.  Referenced data is handled through the Joint_Data.
 *  An iterator is supplied to traverse the array of records that make up the
 *  data.  Fields from the array can be accessed as per standard Array access.
 *  Whilst Joint_Data is retrieved via class properties that are automatically
 *  created from the references passed at construction.
 *
 *  Below is a usage example containing each different type of access:
 *  \code
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
 \endcode
*/
class Base implements \Evoke\Core\Iface\Data
{
	/** The data is protected, which is important to note in this class.  Being
	 *  protected means that it will still be accessible from extended classes,
	 *  however when joint fields are referenced externally this member does not
	 *  get in the way.  This means that data is still a valid name for joining
	 *  data. (External references like $obj->data will not be able to see
	 *  $this->data and will instead find the appropriate joint data).
	 */
	protected $data;

	/// This is the setup, which is protected as per data.
	protected $setup;
   
	public function __construct(Array $setup)
	{
		$this->data = array();
		$this->setup = array_merge(
			array('Joint_Key'  => 'Joint_Data',
			      'References' => NULL),
			$setup);

		if (!is_array($this->setup['References']))
		{
			throw new \InvalidArgumentException(
				__METHOD__ . ' requires References as array');
		}

		foreach ($this->setup['References'] as $parentField => $dataContainer)
		{
			if (!$dataContainer instanceof \Evoke\Data\Base)
			{
				throw new \InvalidArgumentException(
					__METHOD__ . ' requires Data for parent field: ' .
					$parentField);
			}
		}
	}

	/******************/
	/* Public Methods */
	/******************/

	/** Provide access to the referenced data.  This allows the object to be used
	 *  like so:  $object->referencedData (for joint data with a parent field of
	 *  'Referenced_Data').
	 *  @param referenceName \string The parent field for the referenced data.
	 *  This can be as per the return value of \ref getReferenceName.
	 */
	public function __get($referenceName)
	{
		if (isset($this->setup['References'][$referenceName]))
		{
			return $this->setup['References'][$referenceName];
		}
      
		foreach ($this->setup['References'] as $parentField => $dataContainer)
		{
			if ($referenceName === $this->getReferenceName($parentField))
			{
				return $dataContainer;
			}
		}
      
		throw new \OutOfBoundsException(
			__METHOD__ . ' record does not refer to: ' .
			var_export($referenceName, true) . ' references are: ' . var_export($this->setup['References'], true));
	}

	/** Get the current record as a simple array (without iterator or reference
	 *  access).
	 *  \return Array The record that we are managing.
	 */
	public function getRecord()
	{
		return current($this->data);
	}

	/** Return whether the data is empty or not.
	 *  \return \bool Whether the data is empty or not.
	 */
	public function isEmpty()
	{
		return empty($this->data);
	}
   
	/** Set the data that we are managing.
	 *  @param \array The data we want to manage.
	 */
	public function setData(Array $data)
	{
		if (!is_array($data))
		{
			throw new \InvalidArgumentException(
				__METHOD__ . ' requires data as an array');
		}

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
	 *  \return \bool Whether the current data record is valid.
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
	 *  but we don't want the element to change.
	 */
	public function offsetSet($offset, $value)
	{
		throw new \RuntimeException(
			__METHOD__ . ' should never be called - data is only transferrable ' .
			'it is not to be modified.');
	}

	/** We are required to make these available to complete the interface,
	 *  but we don't want the element to change.
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
		$jointKey = $this->setup['Joint_Key'];

		foreach ($this->setup['References'] as $parentField => $data)
		{
			if (isset($record[$jointKey][$parentField]))
			{
				$data->setData($record[$jointKey][$parentField]);
			}
		}
	}
     
	/*******************/
	/* Private Methods */
	/*******************/

	/** Get the reference name that will be used for accessing the joint data
	 *  from this object.  It should match our standard naming of properties
	 *  (camel case) and not contain the final ID which is not needed.
	 *  @param parentField \string The parent field for the joint data.
	 *  \return \string The reference name.
	 */
	private function getReferenceName($parentField)
	{
		$nameParts = mb_split('_', $parentField);
		$lastPart = end($nameParts);

		// Remove any final id.
		if (mb_strtolower($lastPart) === 'id')
		{
			array_pop($nameParts);
		}

		$name = '';

		foreach ($nameParts as $part)
		{
			$name .= $part;
		}
      
		$name[0] = mb_strtolower($name[0]);
      
		return $name;
	}
}
// EOF